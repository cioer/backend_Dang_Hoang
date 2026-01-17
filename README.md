# School Management System - Backend

A PHP-based REST API backend for school management system with features including student violation tracking, ranking, scheduling, and user management.

## Features

- **User Management**: Students, Teachers, Parents, Administrators
- **Violation Tracking**: Record and track student violations with point deduction
- **Ranking System**: Real-time student rankings by class, weekly, monthly, semester
- **Schedule Management**: Class schedules and teacher assignments
- **Red Star Committee**: Special committee for monitoring student behavior
- **Messaging**: Internal messaging system
- **Reports**: Various reports and statistics
- **Banner Management**: Homepage banners with SSE real-time updates

## Tech Stack

- PHP 8.2+ (PHP-FPM)
- MySQL 8.0 / MariaDB 10.6+
- Nginx
- Docker (optional)
- JWT Authentication

## Project Structure

```
backend/
├── api/                    # API endpoints
│   ├── admin/             # Admin endpoints
│   ├── auth/              # Authentication endpoints
│   ├── banners/           # Banner management
│   ├── classes/           # Class management
│   ├── messages/          # Messaging system
│   ├── notifications/     # Notifications
│   ├── parent/            # Parent endpoints
│   ├── profile/           # User profile
│   ├── ranking/           # Ranking system
│   ├── red_committee/     # Red Star Committee
│   ├── reports/           # Reports
│   ├── schedule/          # Schedules
│   ├── scores/            # Score management
│   ├── student/           # Student endpoints
│   ├── teacher/           # Teacher endpoints
│   ├── violations/        # Violation tracking
│   └── bootstrap.php      # API bootstrap
├── config/                # Configuration files
│   ├── database.php       # Database config
│   ├── jwt.php            # JWT config
│   └── captcha.php        # CAPTCHA config
├── src/                   # Source code
│   └── Core/              # Core classes
│       ├── Bootstrap.php
│       ├── Middleware.php
│       ├── Request.php
│       └── Response.php
├── uploads/               # User uploads
├── docker/                # Docker configuration
├── data.sql               # Database schema and sample data
├── docker-compose.yml     # Docker Compose config
├── API.md                 # API documentation
└── README.md              # This file
```

---

## Deployment to Linux VPS

### Prerequisites

- Ubuntu 20.04/22.04 or Debian 11/12
- Root or sudo access
- Domain name (optional, for SSL)

### Option 1: Docker Deployment (Recommended)

#### Step 1: Install Docker

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install Docker
curl -fsSL https://get.docker.com -o get-docker.sh
sudo sh get-docker.sh

# Install Docker Compose
sudo apt install docker-compose-plugin -y

# Add user to docker group
sudo usermod -aG docker $USER
newgrp docker
```

#### Step 2: Clone and Configure

```bash
# Clone repository
git clone https://github.com/your-repo/backend.git
cd backend

# Create environment file
cp .env.example .env

# Edit configuration
nano .env
```

**.env configuration:**
```env
# Database
DB_HOST=mysql
DB_NAME=school_management
DB_USER=school_user
DB_PASS=your_secure_password_here

# JWT Secret (generate with: openssl rand -base64 32)
JWT_SECRET=your_jwt_secret_key_here

# Ports
APP_PORT=80
DB_PORT=3306

# Environment
APP_ENV=production
```

#### Step 3: Deploy

```bash
# Build and start containers
docker compose up -d --build

# Import database
docker exec -i school_management_db mysql -uschool_user -p'your_password' school_management < data.sql

# Check status
docker compose ps
```

#### Step 4: Configure SSL (with Nginx Proxy)

Create `docker-compose.override.yml`:

```yaml
version: '3.8'
services:
  nginx-proxy:
    image: nginxproxy/nginx-proxy
    ports:
      - "80:80"
      - "443:443"
    volumes:
      - /var/run/docker.sock:/tmp/docker.sock:ro
      - certs:/etc/nginx/certs
      - vhost:/etc/nginx/vhost.d
      - html:/usr/share/nginx/html
    restart: always

  acme-companion:
    image: nginxproxy/acme-companion
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock:ro
      - certs:/etc/nginx/certs
      - vhost:/etc/nginx/vhost.d
      - html:/usr/share/nginx/html
      - acme:/etc/acme.sh
    environment:
      - DEFAULT_EMAIL=your-email@example.com
    restart: always

  app:
    environment:
      - VIRTUAL_HOST=api.your-domain.com
      - LETSENCRYPT_HOST=api.your-domain.com
    expose:
      - "80"

volumes:
  certs:
  vhost:
  html:
  acme:
```

```bash
docker compose -f docker-compose.yml -f docker-compose.override.yml up -d
```

---

### Option 2: Manual Installation

#### Step 1: Install Required Packages

```bash
# Update system
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update
sudo apt install -y php8.2-fpm php8.2-mysql php8.2-mbstring php8.2-xml php8.2-curl php8.2-gd php8.2-zip

# Install Nginx
sudo apt install -y nginx

# Install MySQL/MariaDB
sudo apt install -y mariadb-server mariadb-client
```

#### Step 2: Configure MySQL

```bash
# Secure MySQL installation
sudo mysql_secure_installation

# Create database and user
sudo mysql -u root -p
```

```sql
CREATE DATABASE school_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'school_user'@'localhost' IDENTIFIED BY 'your_secure_password';
GRANT ALL PRIVILEGES ON school_management.* TO 'school_user'@'localhost';
FLUSH PRIVILEGES;
EXIT;
```

```bash
# Import database
mysql -u school_user -p school_management < data.sql
```

#### Step 3: Deploy Application

```bash
# Create web directory
sudo mkdir -p /var/www/school-api
sudo chown -R $USER:$USER /var/www/school-api

# Copy files
cp -r /path/to/backend/* /var/www/school-api/

# Set permissions
sudo chown -R www-data:www-data /var/www/school-api
sudo chmod -R 755 /var/www/school-api
sudo chmod -R 775 /var/www/school-api/uploads
```

#### Step 4: Configure Application

Edit `/var/www/school-api/config/database.php`:

```php
<?php
class Database {
    private $host = "localhost";
    private $db_name = "school_management";
    private $username = "school_user";
    private $password = "your_secure_password";
    public $conn;

    public function getConnection() {
        $this->conn = null;
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name . ";charset=utf8mb4",
                $this->username,
                $this->password,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
                ]
            );
        } catch(PDOException $exception) {
            error_log("Connection error: " . $exception->getMessage());
        }
        return $this->conn;
    }
}
```

Edit `/var/www/school-api/config/jwt.php` - Update `JWT_SECRET`:

```php
define('JWT_SECRET', 'your_very_long_and_secure_jwt_secret_key_here');
```

#### Step 5: Configure Nginx

Create `/etc/nginx/sites-available/school-api`:

```nginx
server {
    listen 80;
    server_name api.your-domain.com;
    root /var/www/school-api;
    index index.php;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Logs
    access_log /var/log/nginx/school-api-access.log;
    error_log /var/log/nginx/school-api-error.log;

    # Max upload size
    client_max_body_size 10M;

    # PHP handling
    location ~ \.php$ {
        try_files $uri =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Uploads directory
    location /uploads/ {
        alias /var/www/school-api/uploads/;
        expires 30d;
        add_header Cache-Control "public, immutable";
    }

    # Deny access to sensitive files
    location ~ /\.(env|git|htaccess) {
        deny all;
    }

    location ~ /(config|src|vendor)/ {
        deny all;
    }

    # Handle API requests
    location /api/ {
        try_files $uri $uri/ /api/index.php?$query_string;
    }
}
```

```bash
# Enable site
sudo ln -s /etc/nginx/sites-available/school-api /etc/nginx/sites-enabled/

# Test config
sudo nginx -t

# Restart services
sudo systemctl restart nginx php8.2-fpm
```

#### Step 6: Install SSL with Certbot

```bash
# Install Certbot
sudo apt install -y certbot python3-certbot-nginx

# Get SSL certificate
sudo certbot --nginx -d api.your-domain.com

# Auto-renewal (already configured by default)
sudo systemctl status certbot.timer
```

---

## Configuration

### Environment Variables

| Variable | Description | Default |
|----------|-------------|---------|
| `DB_HOST` | Database host | `localhost` |
| `DB_NAME` | Database name | `school_management` |
| `DB_USER` | Database user | `school_user` |
| `DB_PASS` | Database password | - |
| `JWT_SECRET` | JWT signing secret | - |
| `APP_ENV` | Environment (development/production) | `production` |

### JWT Configuration

Generate a secure JWT secret:

```bash
openssl rand -base64 32
```

### File Upload Limits

- Avatar: 2MB (JPG, PNG)
- Banner: 5MB (JPG, PNG)
- Configure in `php.ini`:

```ini
upload_max_filesize = 10M
post_max_size = 12M
```

---

## Maintenance

### Backup Database

```bash
# Manual backup
mysqldump -u school_user -p school_management > backup_$(date +%Y%m%d).sql

# Docker backup
docker exec school_management_db mysqldump -uschool_user -p'password' school_management > backup.sql
```

### Restore Database

```bash
# Manual restore
mysql -u school_user -p school_management < backup.sql

# Docker restore
docker exec -i school_management_db mysql -uschool_user -p'password' school_management < backup.sql
```

### View Logs

```bash
# Docker logs
docker compose logs -f app
docker compose logs -f mysql

# Manual installation logs
sudo tail -f /var/log/nginx/school-api-error.log
sudo tail -f /var/log/php8.2-fpm.log
```

### Update Application

```bash
# Docker update
cd /path/to/backend
git pull
docker compose down
docker compose up -d --build

# Manual update
cd /var/www/school-api
git pull
sudo chown -R www-data:www-data .
sudo systemctl restart php8.2-fpm
```

---

## Troubleshooting

### Common Issues

**1. 502 Bad Gateway**
```bash
# Check PHP-FPM status
sudo systemctl status php8.2-fpm

# Check socket file
ls -la /var/run/php/php8.2-fpm.sock

# Restart PHP-FPM
sudo systemctl restart php8.2-fpm
```

**2. Permission Denied**
```bash
# Fix ownership
sudo chown -R www-data:www-data /var/www/school-api
sudo chmod -R 755 /var/www/school-api
sudo chmod -R 775 /var/www/school-api/uploads
```

**3. Database Connection Failed**
```bash
# Test connection
mysql -u school_user -p -h localhost school_management

# Check MySQL status
sudo systemctl status mariadb
```

**4. JWT Token Invalid**
- Ensure `JWT_SECRET` is consistent across deployments
- Check token expiration (default: 7 days)
- Verify Authorization header format: `Bearer <token>`

**5. CORS Issues**
- API already includes CORS headers
- For custom domains, update allowed origins in `Middleware.php`

---

## Security Recommendations

1. **Use HTTPS** - Always use SSL/TLS in production
2. **Firewall** - Only expose ports 80/443
   ```bash
   sudo ufw allow 22
   sudo ufw allow 80
   sudo ufw allow 443
   sudo ufw enable
   ```
3. **Strong Passwords** - Use complex passwords for database and JWT
4. **Regular Updates** - Keep system and packages updated
5. **Backup** - Schedule regular database backups
6. **Monitoring** - Set up log monitoring and alerts

---

## API Documentation

See [API.md](API.md) for complete API documentation.

---

## License

MIT License

---

## Support

For issues and feature requests, please create an issue in the repository.
