#!/bin/bash

# ==========================================
# SCHOOL MANAGEMENT - DEPLOY SCRIPT
# ==========================================

set -e

echo "=========================================="
echo "  SCHOOL MANAGEMENT BACKEND DEPLOY"
echo "=========================================="

# Màu sắc
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Xác định lệnh docker compose (hỗ trợ cả bản cũ và mới)
if docker compose version &> /dev/null; then
    DOCKER_COMPOSE="docker compose"
elif command -v docker-compose &> /dev/null; then
    DOCKER_COMPOSE="docker-compose"
else
    echo -e "${RED}Lỗi: Docker Compose chưa được cài đặt!${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Sử dụng: $DOCKER_COMPOSE${NC}"

# Kiểm tra Docker
if ! command -v docker &> /dev/null; then
    echo -e "${RED}Lỗi: Docker chưa được cài đặt!${NC}"
    echo "Vui lòng cài đặt Docker tại: https://docs.docker.com/get-docker/"
    exit 1
fi

# Tạo file .env nếu chưa có
if [ ! -f .env ]; then
    echo -e "${YELLOW}Chưa có file .env, đang tạo từ .env.example...${NC}"
    cp .env.example .env

    # Tạo mật khẩu ngẫu nhiên
    RANDOM_PASSWORD=$(openssl rand -base64 16 | tr -d "=+/" | cut -c1-16)
    RANDOM_ROOT_PASSWORD=$(openssl rand -base64 20 | tr -d "=+/" | cut -c1-20)

    # Cập nhật .env với mật khẩu ngẫu nhiên
    sed -i "s/DB_PASSWORD=.*/DB_PASSWORD=$RANDOM_PASSWORD/" .env
    sed -i "s/DB_ROOT_PASSWORD=.*/DB_ROOT_PASSWORD=$RANDOM_ROOT_PASSWORD/" .env

    echo -e "${GREEN}✓ Đã tạo file .env với mật khẩu ngẫu nhiên${NC}"
    echo -e "${RED}⚠ MẬT KHẨU CỦA BẠN:${NC}"
    echo "   DB_PASSWORD: $RANDOM_PASSWORD"
    echo "   DB_ROOT_PASSWORD: $RANDOM_ROOT_PASSWORD"
    echo ""
    read -p "Nhấn Enter để tiếp tục..."
fi

# Tạo thư mục uploads
mkdir -p uploads

# Dừng containers cũ nếu có
echo -e "${YELLOW}Dừng containers cũ...${NC}"
$DOCKER_COMPOSE down 2>/dev/null || true

# Xóa volume cũ nếu muốn reset database
if [ "$1" == "--reset-db" ]; then
    echo -e "${RED}⚠ CẢNH BÁO: Xóa toàn bộ dữ liệu database cũ!${NC}"
    read -p "Bạn có chắc không? (y/N): " confirm
    if [ "$confirm" == "y" ] || [ "$confirm" == "Y" ]; then
        $DOCKER_COMPOSE down -v
        echo -e "${GREEN}✓ Đã xóa volume database${NC}"
    fi
fi

# Build và start
echo -e "${YELLOW}Đang build Docker images...${NC}"
$DOCKER_COMPOSE build --no-cache

echo -e "${YELLOW}Đang start services...${NC}"
$DOCKER_COMPOSE up -d

# Chờ MySQL khởi động
echo -e "${YELLOW}Chờ MySQL khởi động (khoảng 30s)...${NC}"
for i in {1..30}; do
    if docker exec school_management_db mysqladmin ping -h localhost -uroot -p$(grep DB_ROOT_PASSWORD .env | cut -d'=' -f2) &> /dev/null; then
        echo -e "${GREEN}✓ MySQL đã sẵn sàng!${NC}"
        break
    fi
    echo -n "."
    sleep 1
done

# Kiểm tra trạng thái
echo ""
echo -e "${GREEN}=========================================="
echo "  DEPLOY THÀNH CÔNG!"
echo "==========================================${NC}"
echo ""
echo "Services đang chạy:"
$DOCKER_COMPOSE ps

echo ""
echo -e "${GREEN}=========================================="
echo "  THÔNG TIN TRUY CẬP"
echo "==========================================${NC}"
echo -e "🌐 API Endpoint: ${YELLOW}http://localhost:$(grep APP_PORT .env | cut -d'=' -f2)/api/${NC}"
echo -e "👤 phpMyAdmin:   ${YELLOW}http://localhost:$(grep PMA_PORT .env | cut -d'=' -f2)${NC}"
echo -e "📊 Database:     ${YELLOW}$(grep DB_NAME .env | cut -d'=' -f2)${NC}"
echo -e "👤 DB User:      ${YELLOW}$(grep DB_USER .env | cut -d'=' -f2)${NC}"
echo ""
echo -e "${YELLOW}Tài khoản mặc định:${NC}"
echo -e "  Admin:  username=${GREEN}admin${NC}, password=${GREEN}password${NC}"
echo -e "  GV:     username=${GREEN}GV-001${NC}, password=${GREEN}password${NC}"
echo -e "  HS:     username=${GREEN}HS-001${NC}, password=${GREEN}password${NC}"
echo ""
echo -e "${YELLOW}Lệnh hữu ích:${NC}"
echo "  $DOCKER_COMPOSE logs -f app      # Xem logs"
echo "  $DOCKER_COMPOSE exec app bash     # Vào container"
echo "  $DOCKER_COMPOSE down              # Dừng services"
echo "  $DOCKER_COMPOSE down -v           # Dừng và xóa database"
echo ""
