# Automated Permission Fix System

## Vấn đề
Mỗi khi cập nhật code mới (git pull hoặc GitHub webhook), files có owner sai (root:root) thay vì www-data:www-data, gây lỗi 500 khi PHP cố gắng truy cập.

## Giải pháp
Hệ thống tự động fix permissions sau mỗi lần cập nhật code.

### 1. Script tự động: `scripts/fix-permissions.sh`
- Fix owner: `www-data:www-data` (UID 82)
- Fix permissions: Files `664`, Directories `775`
- Apply cho: `api/`, `uploads/`, `lib/`, `config/`

### 2. Git Hook: `.git/hooks/post-merge`
- Tự động chạy sau `git pull` hoặc `git merge`
- Gọi `fix-permissions.sh` qua Docker

### 3. Webhook: `webhooks/github_webhook.py`
- GitHub webhook trigger sau mỗi push
- Tự động git pull + fix permissions

## Sử dụng

### Chạy thủ công
```bash
# Trên VPS
cd /home/deploy/backend_Dang_Hoang
bash scripts/fix-permissions.sh
```

### Tự động (đã setup)
- ✅ Sau git pull → Git hook tự động fix
- ✅ Sau GitHub push → Webhook tự động pull + fix
- ✅ Không cần can thiệp thủ công

## Test
```bash
# Test git hook
git pull

# Test webhook (GitHub tự động trigger)
git push

# Verify permissions
ls -la api/teacher/
```

## Expected Output
```
-rw-rw-r-- 1 82 82 2818  api/teacher/get_student_ranking.php
-rw-rw-r-- 1 82 82 2509  api/reports/get_competition_stats.php
drwxrwxr-x 2 82 82 4096  uploads/avatars/
drwxrwxr-x 2 82 82 4096  uploads/banners/
```

## Troubleshooting

### Nếu vẫn gặp lỗi 500
```bash
# Chạy fix thủ công
ssh deploy@huuthang.online "cd backend_Dang_Hoang && bash scripts/fix-permissions.sh"

# Check logs
docker logs school_management_backend --tail 50
```

### Nếu git hook không chạy
```bash
# Ensure executable
chmod +x .git/hooks/post-merge

# Test manually
.git/hooks/post-merge
```

### Nếu webhook auto-fix không hoạt động
```bash
# Check webhook logs
docker logs backend_webhook --tail 50

# Nếu thấy lỗi "docker: command not found" hoặc "✗ Failed"
# Xem hướng dẫn chi tiết tại:
docs/FIX_WEBHOOK_DOCKER_ACCESS.md
```
# Test auto-fix permissions on Tue Jan 20 08:19:54 AM +07 2026
# Test auto-fix with new webhook - Tue Jan 20 08:39:13 AM +07 2026
# Final test 1768873267
