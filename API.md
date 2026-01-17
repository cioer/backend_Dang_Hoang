# School Management System - API Documentation

## Overview

Base URL: `https://your-domain.com/api`

All responses are in JSON format with UTF-8 encoding.

### Authentication

Most endpoints require JWT authentication. Include the token in the Authorization header:

```
Authorization: Bearer <your_jwt_token>
```

### Response Format

**Success Response:**
```json
{
  "data": [...],
  "message": "Success message"
}
```

**Error Response:**
```json
{
  "message": "Error description",
  "code": "error_code"
}
```

**Paginated Response:**
```json
{
  "data": [...],
  "pagination": {
    "current_page": 1,
    "total_pages": 10,
    "total_records": 100,
    "limit": 20
  }
}
```

### User Roles

| Role | Description |
|------|-------------|
| `admin` | System administrator |
| `teacher` | Teacher/Staff |
| `student` | Student |
| `parent` | Parent/Guardian |
| `red_star` | Red Star Committee member |

---

## Authentication Endpoints

### POST /auth/login_teacher.php

Login for teachers and administrators.

**Request:**
```json
{
  "username": "GV-00001",
  "password": "your_password",
  "captcha_token": "optional_if_required",
  "captcha_answer": "optional_if_required"
}
```

**Response (Success):**
```json
{
  "message": "Đăng nhập thành công",
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "user": {
    "id": 1,
    "username": "GV-00001",
    "full_name": "Nguyen Van A",
    "role": "teacher"
  }
}
```

**Response (Captcha Required):**
```json
{
  "message": "Yêu cầu xác minh CAPTCHA",
  "code": "captcha_required",
  "captcha_required": true,
  "captcha_question": "5 + 3 = ?",
  "captcha_token": "abc123..."
}
```

---

### POST /auth/login_student.php

Login for students.

**Request:**
```json
{
  "identifier": "HS-00001",
  "password": "your_password"
}
```

**Response:**
```json
{
  "message": "Đăng nhập thành công",
  "token": "eyJhbGciOiJIUzI1NiIs...",
  "require_change": false,
  "user": {
    "id": 3,
    "username": "HS-00001",
    "full_name": "Tran Van B",
    "role": "student",
    "is_red_star": 0
  }
}
```

---

### POST /auth/login_parent_password.php

Login for parents.

**Request:**
```json
{
  "identifier": "PH-00001",
  "password": "your_password"
}
```

---

### POST /auth/change_password.php

Change user password. **Requires authentication.**

**Request:**
```json
{
  "current_password": "old_password",
  "new_password": "new_password"
}
```

---

### POST /auth/request_otp.php

Request OTP for password reset.

**Request:**
```json
{
  "email": "user@example.com"
}
```

---

### POST /auth/verify_otp.php

Verify OTP code.

**Request:**
```json
{
  "email": "user@example.com",
  "otp": "123456"
}
```

---

### GET /auth/login_status.php

Check current login status. **Requires authentication.**

**Response:**
```json
{
  "logged_in": true,
  "user": {
    "id": 1,
    "username": "admin",
    "role": "admin"
  }
}
```

---

## Profile Endpoints

### GET /profile/get.php

Get current user profile. **Requires authentication.**

**Response:**
```json
{
  "id": 1,
  "username": "admin",
  "full_name": "Quản trị viên",
  "role": "admin",
  "email": "admin@school.edu.vn",
  "phone": "0901234567",
  "avatar": "uploads/avatars/avatar_1.jpg",
  "is_red_star": 0
}
```

---

### POST /profile/update.php

Update user profile. **Requires authentication.**

**Request:**
```json
{
  "email": "newemail@example.com",
  "phone": "0909876543",
  "full_name": "New Name"
}
```

**Response:**
```json
{
  "message": "Updated"
}
```

---

### POST /profile/upload_avatar.php

Upload avatar image. **Requires authentication.**

**Request:** `multipart/form-data`
- `avatar`: Image file (JPG, PNG, max 2MB)

**Response:**
```json
{
  "message": "Avatar uploaded",
  "avatar_url": "uploads/avatars/avatar_1_123456.jpg"
}
```

---

### GET /profile/is_profile_complete.php

Check if profile is complete. **Requires authentication.**

---

## Classes Endpoints

### GET /classes/list_all.php

List all classes. **Public endpoint.**

**Response:**
```json
[
  {"id": 1, "name": "10A1"},
  {"id": 2, "name": "10A2"},
  {"id": 3, "name": "11A1"}
]
```

---

### GET /classes/of_student.php

Get class of a student. **Requires authentication.**

**Query Parameters:**
- `student_id` (optional): Student ID. Defaults to current user.

---

## Subjects Endpoints

### GET /subjects/list_all.php

List all subjects. **Public endpoint.**

**Response:**
```json
[
  {"id": 1, "name": "Toán"},
  {"id": 2, "name": "Văn"},
  {"id": 3, "name": "Anh"}
]
```

---

## Violations Endpoints

### GET /violations/get_rules.php

Get all violation rules. **Requires authentication.**

**Response:**
```json
[
  {
    "id": 1,
    "rule_name": "Đi học muộn",
    "points": 2,
    "description": "Đến lớp sau giờ vào học"
  },
  {
    "id": 2,
    "rule_name": "Không mặc đồng phục",
    "points": 2,
    "description": "Thiếu đồng phục theo quy định"
  }
]
```

---

### POST /violations/submit.php

Submit a violation. **Requires teacher/red_star role.**

**Request:**
```json
{
  "student_id": 123,
  "rule_id": 1,
  "evidence": "Description of the violation"
}
```

**Response:**
```json
{
  "message": "OK",
  "points": "96",
  "deduct": 2,
  "prev_points": 98
}
```

---

### GET /violations/get_points.php

Get violation points for a student. **Requires authentication.**

**Query Parameters:**
- `student_id`: Student ID

---

### GET /violations/get_stats.php

Get violation statistics. **Requires authentication.**

---

### GET /violations/list_students_by_class.php

List students in a class with violation info. **Requires authentication.**

**Query Parameters:**
- `class_id`: Class ID

---

## Ranking Endpoints

### GET /ranking/get_current.php

Get current ranking for a class. **Requires authentication.**

**Query Parameters:**
- `class_id`: Class ID

**Response:**
```json
[
  {
    "rank": 1,
    "student_id": 127,
    "student_name": "Bùi Hải Vy",
    "student_code": "HS-10A1-17",
    "points_lost": 0,
    "current_score": 100,
    "grade": "Tốt"
  },
  {
    "rank": 2,
    "student_id": 126,
    "student_name": "Hoàng Minh Vũ",
    "student_code": "HS-10A1-16",
    "points_lost": 2,
    "current_score": 98,
    "grade": "Tốt"
  }
]
```

---

### GET /ranking/get_weekly.php

Get weekly ranking.

---

### GET /ranking/get_monthly.php

Get monthly ranking.

---

### GET /ranking/get_semester.php

Get semester ranking.

---

### GET /ranking/export_csv.php

Export ranking to CSV. **Requires admin role.**

**Query Parameters:**
- `class_id`: Class ID
- `period`: `current`, `weekly`, `monthly`, `semester`

---

## Teacher Endpoints

### GET /teacher/get_classes.php

Get classes assigned to the teacher. **Requires teacher role.**

**Response:**
```json
[
  {"id": 3, "name": "10A2"},
  {"id": 6, "name": "11A2"}
]
```

---

### GET /teacher/get_students.php

Get students in a class. **Requires teacher role.**

**Query Parameters:**
- `class_id`: Class ID

**Response:**
```json
[
  {
    "id": 141,
    "full_name": "Dào Ngọc Thiện Nhân",
    "code": "HS-10A2-11",
    "avatar": null
  }
]
```

---

### POST /teacher/request_classes.php

Request to be assigned to classes. **Requires teacher role.**

**Request:**
```json
{
  "class_ids": [1, 2, 3]
}
```

---

### GET /teacher/request_list.php

List pending class requests. **Requires teacher role.**

---

### POST /teacher/students/create.php

Create a new student. **Requires teacher role.**

**Request:**
```json
{
  "username": "HS-NEW-01",
  "full_name": "Student Name",
  "class_id": 1,
  "email": "student@example.com",
  "phone": "0901234567"
}
```

---

### POST /teacher/students/update.php

Update student information. **Requires teacher role.**

---

### DELETE /teacher/students/delete.php

Delete a student. **Requires teacher role.**

---

## Admin Endpoints

### POST /admin/users/list.php

List users by role. **Requires admin role.**

**Request:**
```json
{
  "role": "student",
  "search": "optional_search_term",
  "page": 1,
  "limit": 20
}
```

**Response:**
```json
{
  "data": [
    {
      "id": 252,
      "username": "HS-00001",
      "full_name": "Nguyen Van A",
      "role": "student",
      "avatar": null,
      "email": "student@example.com",
      "phone": "0901234567",
      "is_locked": 0,
      "created_at": "2026-01-14 12:03:24"
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 8,
    "total_records": 157,
    "limit": 20
  }
}
```

---

### POST /admin/users/create.php

Create a new user. **Requires admin role.**

**Request:**
```json
{
  "username": "NEW-USER-01",
  "password": "password123",
  "full_name": "User Name",
  "role": "student",
  "email": "user@example.com",
  "phone": "0901234567"
}
```

---

### GET /admin/users/get.php

Get user details. **Requires admin role.**

**Query Parameters:**
- `id`: User ID

---

### POST /admin/users/update.php

Update user information. **Requires admin role.**

**Request:**
```json
{
  "id": 123,
  "full_name": "Updated Name",
  "email": "newemail@example.com",
  "is_locked": 0
}
```

---

### DELETE /admin/users/delete.php

Delete a user. **Requires admin role.**

**Request:**
```json
{
  "id": 123
}
```

---

### GET /admin/stats/violations.php

Get violation statistics. **Requires admin role.**

**Response:**
```json
[
  {"label": "2026-01-14", "count": 6},
  {"label": "2026-01-12", "count": 2},
  {"label": "2026-01-11", "count": 1}
]
```

---

### GET /admin/stats/violation_details.php

Get detailed violation statistics. **Requires admin role.**

**Query Parameters:**
- `start_date`: Start date (YYYY-MM-DD)
- `end_date`: End date (YYYY-MM-DD)

---

### POST /admin/teacher/create.php

Create a teacher account. **Requires admin role.**

---

### GET /admin/teacher_request/list.php

List pending teacher class requests. **Requires admin role.**

---

### POST /admin/teacher_request/approve.php

Approve/reject teacher class request. **Requires admin role.**

---

### POST /admin/violations/set_thresholds.php

Set violation point thresholds. **Requires admin role.**

---

### POST /admin/schedule/set.php

Set class schedule. **Requires admin role.**

---

### GET /admin/get_background.php

Get background settings. **Requires admin role.**

---

### POST /admin/set_background.php

Set background settings. **Requires admin role.**

---

### POST /admin/upload_background.php

Upload background image. **Requires admin role.**

---

### POST /admin/backup.php

Create database backup. **Requires admin role.**

---

## Banner Endpoints

### GET /banners/get_active.php

Get active banners. **Public endpoint.**

**Response:**
```json
[
  {
    "id": 1,
    "image_url": "uploads/banners/banner1.jpg",
    "title": "Welcome Banner",
    "cta_text": "Xem ngay",
    "link_url": "/news/1"
  }
]
```

---

### POST /banners/upload.php

Upload a new banner. **Requires admin role.**

**Request:** `multipart/form-data`
- `image`: Image file (JPG, PNG, max 5MB)
- `title`: Banner title
- `cta_text`: Call-to-action text
- `link_url`: Link URL
- `priority`: Priority number

---

### POST /banners/delete.php

Delete a banner. **Requires admin role.**

**Request:**
```json
{
  "id": 1
}
```

---

### POST /banners/toggle_active.php

Toggle banner active status. **Requires admin role.**

---

### GET /banners/stream.php

SSE endpoint for real-time banner updates. **Public endpoint.**

---

## Schedule Endpoints

### GET /schedule/get_weekly.php

Get weekly schedule. **Requires authentication.**

**Query Parameters:**
- `class_id`: Class ID

---

### GET /schedule/get_by_teacher.php

Get teacher's schedule. **Requires teacher role.**

---

### POST /schedule/auto_generate.php

Auto-generate schedule. **Requires admin role.**

---

## Messages Endpoints

### GET /messages/get_contacts.php

Get message contacts. **Requires authentication.**

---

### GET /messages/get_conversation.php

Get conversation messages. **Requires authentication.**

**Query Parameters:**
- `contact_id`: Other user's ID

---

### POST /messages/send.php

Send a message. **Requires authentication.**

**Request:**
```json
{
  "recipient_id": 123,
  "content": "Message content"
}
```

---

## Notifications Endpoints

### GET /notifications/get_all.php

Get all notifications. **Requires authentication.**

---

### POST /notifications/create.php

Create a notification. **Requires admin role.**

---

## Parent Endpoints

### GET /parent/list_children.php

List parent's children. **Requires parent role.**

---

### GET /parent/get_child_violations.php

Get child's violations. **Requires parent role.**

**Query Parameters:**
- `child_id`: Child's user ID

---

### GET /parent/get_child_ranking.php

Get child's ranking. **Requires parent role.**

---

## Red Committee Endpoints

### GET /red_committee/list.php

List Red Star Committee members. **Requires admin/teacher role.**

---

### POST /red_committee/add.php

Add member to Red Star Committee. **Requires admin role.**

---

### POST /red_committee/remove.php

Remove member from Red Star Committee. **Requires admin role.**

---

### POST /red_committee/create_account.php

Create Red Star account. **Requires admin role.**

---

### GET /red_committee/logs.php

Get Red Star activity logs. **Requires admin role.**

---

## Reports Endpoints

### GET /reports/get_statistics.php

Get overall statistics. **Requires admin role.**

---

### GET /reports/get_conduct.php

Get conduct reports. **Requires admin/teacher role.**

---

### GET /reports/get_academic.php

Get academic reports. **Requires admin/teacher role.**

---

### GET /reports/get_competition_stats.php

Get competition statistics. **Requires admin role.**

---

## Scores Endpoints

### GET /scores/list_students.php

List students for scoring. **Requires teacher role.**

---

### POST /scores/submit.php

Submit scores. **Requires teacher role.**

---

## Other Endpoints

### GET /captcha/get_challenge.php

Get CAPTCHA challenge. **Public endpoint.**

**Response:**
```json
{
  "question": "5 + 3 = ?",
  "token": "captcha_token_here"
}
```

---

### POST /attendance/submit.php

Submit attendance. **Requires teacher role.**

---

### POST /conduct/submit_result.php

Submit conduct result. **Requires teacher role.**

---

### POST /student/set_class.php

Set student's class. **Requires authentication.**

---

### GET /news/get_all.php

Get all news. **Public endpoint.**

---

### GET /news/get_latest.php

Get latest news. **Public endpoint.**

---

## Error Codes

| HTTP Code | Description |
|-----------|-------------|
| 200 | Success |
| 400 | Bad Request - Missing or invalid parameters |
| 401 | Unauthorized - Invalid or missing token |
| 403 | Forbidden - Insufficient permissions |
| 404 | Not Found - Resource not found |
| 500 | Server Error - Internal server error |

---

## Rate Limiting

- Login endpoints: 5 attempts per 15 minutes per IP
- After 3 failed login attempts, CAPTCHA is required
- General API calls: No strict limit (recommended: 100 requests/minute)

---

## Notes

1. All dates are in `YYYY-MM-DD` format
2. All timestamps are in `YYYY-MM-DD HH:MM:SS` format (UTC+7)
3. File uploads accept JPG/PNG images only
4. Maximum file sizes: Avatar 2MB, Banner 5MB
5. JWT tokens expire after 7 days
