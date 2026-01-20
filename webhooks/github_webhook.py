#!/usr/bin/env python3
import http.server
import subprocess
import json
import hmac
import hashlib
import os
import logging

PORT = 9001
SECRET = os.environ.get('WEBHOOK_SECRET', 'your-webhook-secret-here')

REPOS = {
    'backend_Dang_Hoang': {
        'path': '/home/deploy/backend_Dang_Hoang',
        'branch': 'main'
    },
    'backend_dang_hoang': {
        'path': '/home/deploy/backend_Dang_Hoang',
        'branch': 'main'
    }
}

log_path = '/app/webhook.log' if os.path.isdir('/app') else '/tmp/webhook.log'
logging.basicConfig(
    level=logging.INFO,
    format='%(asctime)s - %(levelname)s - %(message)s',
    handlers=[
        logging.FileHandler(log_path),
        logging.StreamHandler()
    ]
)
logger = logging.getLogger(__name__)

GIT_ENV = os.environ.copy()
GIT_ENV['GIT_CONFIG_COUNT'] = '1'
GIT_ENV['GIT_CONFIG_KEY_0'] = 'safe.directory'
GIT_ENV['GIT_CONFIG_VALUE_0'] = '*'


def verify_signature(payload, signature):
    if not SECRET or SECRET == 'your-webhook-secret-here':
        return True
    if not signature:
        return False
    expected = 'sha256=' + hmac.new(SECRET.encode(), payload, hashlib.sha256).hexdigest()
    return hmac.compare_digest(expected, signature)


def fix_permissions(repo_path):
    """Auto-fix file permissions after git pull"""
    try:
        # Run fix-permissions script via Docker
        result = subprocess.run(
            ['docker', 'exec', 'school_management_backend', 'bash', '/var/www/html/scripts/fix-permissions.sh'],
            cwd=repo_path,
            capture_output=True,
            text=True,
            timeout=60
        )

        if result.returncode == 0:
            logger.info(f'✓ Permissions fixed successfully')
            logger.info(f'Permission fix output: {result.stdout}')
            return True, result.stdout
        else:
            logger.warning(f'⚠ Permission fix returned non-zero: {result.stderr}')
            return False, result.stderr

    except Exception as e:
        logger.error(f'❌ Permission fix failed: {e}')
        return False, str(e)


def git_pull(repo_config):
    path = repo_config['path']
    branch = repo_config.get('branch', 'main')
    try:
        # Git fetch
        result = subprocess.run(['git', 'fetch', 'origin'], cwd=path, capture_output=True, text=True, timeout=60, env=GIT_ENV)
        logger.info(f'Git fetch: {result.stdout} {result.stderr}')

        # Git reset
        result = subprocess.run(['git', 'reset', '--hard', f'origin/{branch}'], cwd=path, capture_output=True, text=True, timeout=60, env=GIT_ENV)
        logger.info(f'Git reset result: {result.stdout} {result.stderr}')

        # Auto-fix permissions after successful pull
        logger.info('🔧 Auto-fixing permissions...')
        perm_success, perm_msg = fix_permissions(path)

        if not perm_success:
            logger.warning(f'Permission fix had issues but continuing: {perm_msg}')

        return True, result.stdout

    except Exception as e:
        logger.error(f'Git pull failed: {e}')
        return False, str(e)


class WebhookHandler(http.server.BaseHTTPRequestHandler):
    def do_POST(self):
        content_length = int(self.headers.get('Content-Length', 0))
        payload = self.rfile.read(content_length)

        signature = self.headers.get('X-Hub-Signature-256')
        if not verify_signature(payload, signature):
            self.send_response(403)
            self.end_headers()
            self.wfile.write(b'Invalid signature')
            return

        try:
            data = json.loads(payload)
        except json.JSONDecodeError:
            self.send_response(400)
            self.end_headers()
            self.wfile.write(b'Invalid JSON')
            return

        repo_name = data.get('repository', {}).get('name', '')
        ref = data.get('ref', '')
        logger.info(f'Received webhook for repo: {repo_name}, ref: {ref}')

        if repo_name not in REPOS:
            self.send_response(200)
            self.end_headers()
            self.wfile.write(b'OK - Not configured')
            return

        repo_config = REPOS[repo_name]
        expected_ref = f"refs/heads/{repo_config.get('branch', 'main')}"

        if ref != expected_ref:
            self.send_response(200)
            self.end_headers()
            self.wfile.write(b'OK - Different branch')
            return

        success, message = git_pull(repo_config)

        if success:
            self.send_response(200)
            self.end_headers()
            self.wfile.write(f'Deployed: {message}'.encode())
        else:
            self.send_response(500)
            self.end_headers()
            self.wfile.write(f'Failed: {message}'.encode())

    def do_GET(self):
        self.send_response(200)
        self.end_headers()
        self.wfile.write(b'Webhook server is running')

    def log_message(self, format, *args):
        logger.info(f'{self.address_string()} - {format % args}')


def main():
    server = http.server.HTTPServer(('0.0.0.0', PORT), WebhookHandler)
    logger.info(f'Webhook server starting on port {PORT}')
    server.serve_forever()


if __name__ == '__main__':
    main()
