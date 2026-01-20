# Fix Webhook Docker Access Issue

## Problem

The automated permission fix system isn't working because the webhook container cannot execute `docker exec` commands. When the webhook tries to run `fix-permissions.sh`, the script fails with:

```
→ Fixing: API files
✗ Failed

→ Fixing: Upload directories
✗ Failed
```

**Root Cause**: The webhook container doesn't have access to the Docker daemon, so it cannot run `docker exec school_management_backend` commands.

## Solution

Mount the Docker socket in the webhook container so it can communicate with the Docker daemon and execute commands in other containers.

## Steps to Fix

### 1. SSH into VPS

```bash
ssh deploy@huuthang.online
```

### 2. Stop the webhook container

```bash
cd ~/webhooks
docker-compose down
```

### 3. Edit docker-compose.yml

```bash
nano docker-compose.yml
```

### 4. Add Docker socket volume mount

Find the `volumes:` section under the webhook service and add this line:

```yaml
volumes:
  - /var/run/docker.sock:/var/run/docker.sock  # Add this line at the top
  - /home/deploy/backend_Dang_Hoang:/home/deploy/backend_Dang_Hoang
  - /home/deploy/backend:/home/deploy/backend
  - /home/deploy/.ssh:/root/.ssh:ro
  - /home/deploy/.gitconfig:/root/.gitconfig:ro
```

**Complete example of webhook docker-compose.yml:**

```yaml
version: '3.8'

services:
  webhook:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: backend_webhook
    restart: unless-stopped
    ports:
      - "9001:9001"
    environment:
      - WEBHOOK_SECRET=${WEBHOOK_SECRET}
    volumes:
      - /var/run/docker.sock:/var/run/docker.sock  # ← NEW LINE
      - /home/deploy/backend_Dang_Hoang:/home/deploy/backend_Dang_Hoang
      - /home/deploy/backend:/home/deploy/backend
      - /home/deploy/.ssh:/root/.ssh:ro
      - /home/deploy/.gitconfig:/root/.gitconfig:ro
    networks:
      - webhook_network

networks:
  webhook_network:
    driver: bridge
```

### 5. Install Docker CLI in webhook container (if needed)

The webhook container also needs the Docker CLI installed. Check the webhook's Dockerfile:

```bash
nano Dockerfile
```

Ensure it includes Docker CLI installation. If not, add:

```dockerfile
FROM python:3.9-slim

# Install Docker CLI
RUN apt-get update && \
    apt-get install -y docker.io && \
    rm -rf /var/lib/apt/lists/*

# ... rest of Dockerfile
```

### 6. Rebuild and restart webhook

```bash
docker-compose build
docker-compose up -d
```

### 7. Verify Docker access

Check if the webhook container can now run Docker commands:

```bash
docker exec backend_webhook docker ps
```

Expected output: List of running containers

### 8. Test the automated permission fix

Trigger a test by pushing a commit to GitHub:

```bash
cd ~/backend_Dang_Hoang
echo "# Test webhook auto-fix $(date +%s)" >> README_PERMISSIONS.md
git add README_PERMISSIONS.md
git commit -m "test: Verify webhook auto-fix after Docker socket mount"
git push origin main
```

### 9. Check webhook logs

```bash
docker logs backend_webhook --tail 50 -f
```

Look for:
```
✓ Running on host/webhook - will use docker exec
→ Fixing: API files
  ✓ Fixed
→ Fixing: Upload directories
  ✓ Fixed
✓ Permission fix completed!
```

### 10. Verify permissions were actually fixed

```bash
docker exec school_management_backend ls -la /var/www/html/api/teacher/get_student_ranking.php
```

Expected output:
```
-rw-rw-r-- 1 82 82 2818 ... api/teacher/get_student_ranking.php
```

## Security Note

Mounting `/var/run/docker.sock` gives the container access to the Docker daemon, which has root-level access to the host. This is necessary for the webhook to fix permissions in other containers, but ensure:

1. The webhook is properly secured with a strong `WEBHOOK_SECRET`
2. The webhook only accepts requests from GitHub's IP ranges
3. The webhook code is reviewed and trusted

## Troubleshooting

### If Docker socket mount doesn't work

Check socket permissions on host:
```bash
ls -la /var/run/docker.sock
```

Should show:
```
srw-rw---- 1 root docker ... /var/run/docker.sock
```

### If Docker CLI is not found

The webhook container needs the `docker` binary installed. Rebuild the Dockerfile with Docker CLI as shown in step 5.

### If permissions still fail

Run the fix script manually to see detailed errors:
```bash
docker exec backend_webhook bash /home/deploy/backend_Dang_Hoang/scripts/fix-permissions.sh
```

## Alternative Approach (if Docker socket doesn't work)

If mounting the Docker socket causes issues, an alternative is to run the permission fix script directly on the host (outside Docker) via systemd or a cron job triggered by the webhook. Contact the system administrator for this approach.
