# Discipleship App API Documentation

## Overview

The Discipleship App provides a comprehensive RESTful API for managing church discipleship programs, including member management, class scheduling, attendance tracking, and mentorship programs.

## Base URL

```
http://localhost:8000/api/v1
```

## Authentication

The API uses Laravel Sanctum for token-based authentication. All protected endpoints require a valid Bearer token in the Authorization header.

### Getting an API Token

**POST** `/auth/login`

```json
{
    "email": "admin@discipleship.local",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@discipleship.local",
            "role": "admin",
            "email_verified_at": "2025-10-21T23:38:47.000000Z"
        },
        "token": "1|LLtkZCUY47B0b1DX9nmGfhhxKThNJiW2FRPYGtUe9c314418",
        "token_type": "Bearer"
    }
}
```

### Using the Token

Include the token in the Authorization header for all protected requests:

```
Authorization: Bearer 1|LLtkZCUY47B0b1DX9nmGfhhxKThNJiW2FRPYGtUe9c314418
```

## User Roles

The application supports four user roles with different permissions:

- **admin**: Full access to all features
- **pastor**: Can manage members, classes, and mentorships
- **coordinator**: Can manage classes and view members
- **member**: Basic access to view their own data

## API Endpoints

### Authentication

#### Login
**POST** `/auth/login`

Authenticate a user and receive an API token.

**Request Body:**
```json
{
    "email": "user@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "User Name",
            "email": "user@example.com",
            "role": "admin",
            "email_verified_at": "2025-10-21T23:38:47.000000Z"
        },
        "token": "1|token_string",
        "token_type": "Bearer"
    }
}
```

#### Logout
**POST** `/auth/logout`

Revoke the current API token.

**Response:**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

#### Get Current User
**GET** `/auth/me`

Get information about the currently authenticated user.

**Response:**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "Admin User",
            "email": "admin@discipleship.local",
            "role": "admin",
            "email_verified_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Get User Permissions
**GET** `/auth/permissions`

Get the permissions for the current user.

**Response:**
```json
{
    "success": true,
    "data": [
        "can_manage_users",
        "can_manage_classes",
        "can_manage_members"
    ]
}
```

### Members

#### List Members
**GET** `/members`

Get a paginated list of members with optional filtering.

**Query Parameters:**
- `search` (string): Search by name or phone
- `conversion_date_from` (date): Filter by conversion date from
- `conversion_date_to` (date): Filter by conversion date to
- `preferred_contact` (string): Filter by preferred contact method
- `page` (integer): Page number for pagination
- `per_page` (integer): Number of items per page

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "full_name": "John Doe",
                "phone": "0712345678",
                "email": "john@example.com",
                "date_of_conversion": "2024-01-01",
                "preferred_contact": "sms",
                "notes": "New member",
                "created_at": "2025-10-21T23:38:47.000000Z",
                "updated_at": "2025-10-21T23:38:47.000000Z"
            }
        ],
        "current_page": 1,
        "total": 50,
        "per_page": 15
    }
}
```

#### Create Member
**POST** `/members`

Create a new member.

**Request Body:**
```json
{
    "full_name": "John Doe",
    "phone": "0712345678",
    "email": "john@example.com",
    "date_of_conversion": "2024-01-01",
    "preferred_contact": "sms",
    "notes": "New member"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Member created successfully",
    "data": {
        "member": {
            "id": 1,
            "full_name": "John Doe",
            "phone": "0712345678",
            "email": "john@example.com",
            "date_of_conversion": "2024-01-01",
            "preferred_contact": "sms",
            "notes": "New member",
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Get Member
**GET** `/members/{id}`

Get a specific member by ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "member": {
            "id": 1,
            "full_name": "John Doe",
            "phone": "0712345678",
            "email": "john@example.com",
            "date_of_conversion": "2024-01-01",
            "preferred_contact": "sms",
            "notes": "New member",
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Update Member
**PUT** `/members/{id}`

Update an existing member.

**Request Body:**
```json
{
    "full_name": "John Smith",
    "phone": "0712345678",
    "email": "john.smith@example.com",
    "date_of_conversion": "2024-01-01",
    "preferred_contact": "email",
    "notes": "Updated member"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Member updated successfully",
    "data": {
        "member": {
            "id": 1,
            "full_name": "John Smith",
            "phone": "0712345678",
            "email": "john.smith@example.com",
            "date_of_conversion": "2024-01-01",
            "preferred_contact": "email",
            "notes": "Updated member",
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Delete Member
**DELETE** `/members/{id}`

Delete a member.

**Response:**
```json
{
    "success": true,
    "message": "Member deleted successfully"
}
```

#### Get Member Attendance
**GET** `/members/{id}/attendance`

Get attendance records for a specific member.

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "class_session_id": 1,
                "member_id": 1,
                "status": "present",
                "marked_by": 2,
                "marked_at": "2025-10-21T23:38:47.000000Z",
                "created_at": "2025-10-21T23:38:47.000000Z",
                "updated_at": "2025-10-21T23:38:47.000000Z"
            }
        ]
    }
}
```

#### Get Member Mentorships
**GET** `/members/{id}/mentorships`

Get mentorship records for a specific member.

**Response:**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "member_id": 1,
            "mentor_id": 2,
            "start_date": "2024-01-01",
            "end_date": null,
            "status": "active",
            "meeting_frequency": "weekly",
            "notes": "Regular mentorship",
            "completed_at": null,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    ]
}
```

#### Get Member Statistics
**GET** `/members/{id}/statistics`

Get statistics for a specific member.

**Response:**
```json
{
    "success": true,
    "data": {
        "total_sessions": 10,
        "present_count": 8,
        "absent_count": 1,
        "excused_count": 1,
        "attendance_rate": 80.0,
        "active_mentorships": 1,
        "completed_mentorships": 0
    }
}
```

### Classes

#### List Classes
**GET** `/classes`

Get a paginated list of discipleship classes.

**Query Parameters:**
- `search` (string): Search by title or description
- `mentor_id` (integer): Filter by mentor ID
- `is_active` (boolean): Filter by active status
- `page` (integer): Page number for pagination
- `per_page` (integer): Number of items per page

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "title": "New Believers Class",
                "description": "Introduction to basic Christian beliefs",
                "mentor_id": 2,
                "capacity": 25,
                "duration_weeks": 8,
                "schedule_type": "weekly",
                "schedule_day": "sunday",
                "schedule_time": "10:00:00",
                "start_date": "2025-09-23",
                "end_date": "2025-11-18",
                "location": "Main Hall",
                "is_active": true,
                "created_at": "2025-10-21T23:38:47.000000Z",
                "updated_at": "2025-10-21T23:38:47.000000Z",
                "mentor": {
                    "id": 2,
                    "name": "Pastor John Smith",
                    "email": "pastor@discipleship.local",
                    "role": "pastor"
                }
            }
        ],
        "current_page": 1,
        "total": 4,
        "per_page": 15
    }
}
```

#### Create Class
**POST** `/classes`

Create a new discipleship class.

**Request Body:**
```json
{
    "title": "New Believers Class",
    "description": "Introduction to basic Christian beliefs",
    "mentor_id": 2,
    "capacity": 25,
    "duration_weeks": 8,
    "schedule_type": "weekly",
    "schedule_day": "sunday",
    "schedule_time": "10:00:00",
    "start_date": "2025-09-23",
    "end_date": "2025-11-18",
    "location": "Main Hall"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Class created successfully",
    "data": {
        "class": {
            "id": 1,
            "title": "New Believers Class",
            "description": "Introduction to basic Christian beliefs",
            "mentor_id": 2,
            "capacity": 25,
            "duration_weeks": 8,
            "schedule_type": "weekly",
            "schedule_day": "sunday",
            "schedule_time": "10:00:00",
            "start_date": "2025-09-23",
            "end_date": "2025-11-18",
            "location": "Main Hall",
            "is_active": true,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Get Class
**GET** `/classes/{id}`

Get a specific class by ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "class": {
            "id": 1,
            "title": "New Believers Class",
            "description": "Introduction to basic Christian beliefs",
            "mentor_id": 2,
            "capacity": 25,
            "duration_weeks": 8,
            "schedule_type": "weekly",
            "schedule_day": "sunday",
            "schedule_time": "10:00:00",
            "start_date": "2025-09-23",
            "end_date": "2025-11-18",
            "location": "Main Hall",
            "is_active": true,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z",
            "mentor": {
                "id": 2,
                "name": "Pastor John Smith",
                "email": "pastor@discipleship.local",
                "role": "pastor"
            },
            "sessions": []
        }
    }
}
```

#### Update Class
**PUT** `/classes/{id}`

Update an existing class.

**Request Body:**
```json
{
    "title": "Updated Class Title",
    "description": "Updated description",
    "mentor_id": 2,
    "capacity": 30,
    "duration_weeks": 10,
    "schedule_type": "weekly",
    "schedule_day": "sunday",
    "schedule_time": "10:00:00",
    "start_date": "2025-09-23",
    "end_date": "2025-11-18",
    "location": "Main Hall"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Class updated successfully",
    "data": {
        "class": {
            "id": 1,
            "title": "Updated Class Title",
            "description": "Updated description",
            "mentor_id": 2,
            "capacity": 30,
            "duration_weeks": 10,
            "schedule_type": "weekly",
            "schedule_day": "sunday",
            "schedule_time": "10:00:00",
            "start_date": "2025-09-23",
            "end_date": "2025-11-18",
            "location": "Main Hall",
            "is_active": true,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Delete Class
**DELETE** `/classes/{id}`

Delete a class.

**Response:**
```json
{
    "success": true,
    "message": "Class deleted successfully"
}
```

### Sessions

#### List Sessions
**GET** `/sessions`

Get a paginated list of class sessions.

**Query Parameters:**
- `class_id` (integer): Filter by class ID
- `date_from` (date): Filter by date from
- `date_to` (date): Filter by date to
- `page` (integer): Page number for pagination
- `per_page` (integer): Number of items per page

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "class_id": 1,
                "session_date": "2025-10-21",
                "topic": "Introduction to Salvation",
                "notes": "First session notes",
                "location": "Main Hall",
                "created_by": 2,
                "created_at": "2025-10-21T23:38:47.000000Z",
                "updated_at": "2025-10-21T23:38:47.000000Z"
            }
        ],
        "current_page": 1,
        "total": 10,
        "per_page": 15
    }
}
```

#### Create Session
**POST** `/sessions`

Create a new class session.

**Request Body:**
```json
{
    "class_id": 1,
    "session_date": "2025-10-21",
    "topic": "Introduction to Salvation",
    "notes": "First session notes",
    "location": "Main Hall"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Session created successfully",
    "data": {
        "session": {
            "id": 1,
            "class_id": 1,
            "session_date": "2025-10-21",
            "topic": "Introduction to Salvation",
            "notes": "First session notes",
            "location": "Main Hall",
            "created_by": 2,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Get Session
**GET** `/sessions/{id}`

Get a specific session by ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "session": {
            "id": 1,
            "class_id": 1,
            "session_date": "2025-10-21",
            "topic": "Introduction to Salvation",
            "notes": "First session notes",
            "location": "Main Hall",
            "created_by": 2,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z",
            "class": {
                "id": 1,
                "title": "New Believers Class",
                "mentor": {
                    "name": "Pastor John Smith"
                }
            },
            "attendance": []
        }
    }
}
```

#### Update Session
**PUT** `/sessions/{id}`

Update an existing session.

**Request Body:**
```json
{
    "class_id": 1,
    "session_date": "2025-10-21",
    "topic": "Updated Topic",
    "notes": "Updated notes",
    "location": "Main Hall"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Session updated successfully",
    "data": {
        "session": {
            "id": 1,
            "class_id": 1,
            "session_date": "2025-10-21",
            "topic": "Updated Topic",
            "notes": "Updated notes",
            "location": "Main Hall",
            "created_by": 2,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Delete Session
**DELETE** `/sessions/{id}`

Delete a session.

**Response:**
```json
{
    "success": true,
    "message": "Session deleted successfully"
}
```

### Attendance

#### List Attendance
**GET** `/attendance`

Get a paginated list of attendance records.

**Query Parameters:**
- `class_session_id` (integer): Filter by session ID
- `member_id` (integer): Filter by member ID
- `status` (string): Filter by status (present, absent, excused)
- `page` (integer): Page number for pagination
- `per_page` (integer): Number of items per page

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "class_session_id": 1,
                "member_id": 1,
                "status": "present",
                "marked_by": 2,
                "marked_at": "2025-10-21T23:38:47.000000Z",
                "created_at": "2025-10-21T23:38:47.000000Z",
                "updated_at": "2025-10-21T23:38:47.000000Z"
            }
        ],
        "current_page": 1,
        "total": 50,
        "per_page": 15
    }
}
```

#### Create Attendance
**POST** `/attendance`

Create a new attendance record.

**Request Body:**
```json
{
    "class_session_id": 1,
    "member_id": 1,
    "status": "present"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Attendance recorded successfully",
    "data": {
        "attendance": {
            "id": 1,
            "class_session_id": 1,
            "member_id": 1,
            "status": "present",
            "marked_by": 2,
            "marked_at": "2025-10-21T23:38:47.000000Z",
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Bulk Attendance
**POST** `/attendance/bulk`

Record attendance for multiple members at once.

**Request Body:**
```json
{
    "class_session_id": 1,
    "attendance": [
        {
            "member_id": 1,
            "status": "present"
        },
        {
            "member_id": 2,
            "status": "absent"
        },
        {
            "member_id": 3,
            "status": "excused"
        }
    ]
}
```

**Response:**
```json
{
    "success": true,
    "message": "Bulk attendance recorded successfully",
    "data": {
        "created_count": 3,
        "updated_count": 0
    }
}
```

#### Update Attendance
**PUT** `/attendance/{id}`

Update an existing attendance record.

**Request Body:**
```json
{
    "status": "excused"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Attendance updated successfully",
    "data": {
        "attendance": {
            "id": 1,
            "class_session_id": 1,
            "member_id": 1,
            "status": "excused",
            "marked_by": 2,
            "marked_at": "2025-10-21T23:38:47.000000Z",
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Delete Attendance
**DELETE** `/attendance/{id}`

Delete an attendance record.

**Response:**
```json
{
    "success": true,
    "message": "Attendance record deleted successfully"
}
```

### Mentorships

#### List Mentorships
**GET** `/mentorships`

Get a paginated list of mentorship records.

**Query Parameters:**
- `member_id` (integer): Filter by member ID
- `mentor_id` (integer): Filter by mentor ID
- `status` (string): Filter by status (active, completed, paused)
- `page` (integer): Page number for pagination
- `per_page` (integer): Number of items per page

**Response:**
```json
{
    "success": true,
    "data": {
        "data": [
            {
                "id": 1,
                "member_id": 1,
                "mentor_id": 2,
                "start_date": "2024-01-01",
                "end_date": null,
                "status": "active",
                "meeting_frequency": "weekly",
                "notes": "Regular mentorship",
                "completed_at": null,
                "created_at": "2025-10-21T23:38:47.000000Z",
                "updated_at": "2025-10-21T23:38:47.000000Z"
            }
        ],
        "current_page": 1,
        "total": 10,
        "per_page": 15
    }
}
```

#### Create Mentorship
**POST** `/mentorships`

Create a new mentorship record.

**Request Body:**
```json
{
    "member_id": 1,
    "mentor_id": 2,
    "start_date": "2024-01-01",
    "status": "active",
    "meeting_frequency": "weekly",
    "notes": "Regular mentorship"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Mentorship created successfully",
    "data": {
        "mentorship": {
            "id": 1,
            "member_id": 1,
            "mentor_id": 2,
            "start_date": "2024-01-01",
            "end_date": null,
            "status": "active",
            "meeting_frequency": "weekly",
            "notes": "Regular mentorship",
            "completed_at": null,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Get Mentorship
**GET** `/mentorships/{id}`

Get a specific mentorship by ID.

**Response:**
```json
{
    "success": true,
    "data": {
        "mentorship": {
            "id": 1,
            "member_id": 1,
            "mentor_id": 2,
            "start_date": "2024-01-01",
            "end_date": null,
            "status": "active",
            "meeting_frequency": "weekly",
            "notes": "Regular mentorship",
            "completed_at": null,
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z",
            "member": {
                "id": 1,
                "full_name": "John Doe",
                "phone": "0712345678",
                "email": "john@example.com"
            },
            "mentor": {
                "id": 2,
                "name": "Pastor John Smith",
                "email": "pastor@discipleship.local",
                "role": "pastor"
            }
        }
    }
}
```

#### Update Mentorship
**PUT** `/mentorships/{id}`

Update an existing mentorship.

**Request Body:**
```json
{
    "member_id": 1,
    "mentor_id": 2,
    "start_date": "2024-01-01",
    "end_date": "2024-06-01",
    "status": "completed",
    "meeting_frequency": "weekly",
    "notes": "Completed mentorship"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Mentorship updated successfully",
    "data": {
        "mentorship": {
            "id": 1,
            "member_id": 1,
            "mentor_id": 2,
            "start_date": "2024-01-01",
            "end_date": "2024-06-01",
            "status": "completed",
            "meeting_frequency": "weekly",
            "notes": "Completed mentorship",
            "completed_at": "2025-10-21T23:38:47.000000Z",
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Update Mentorship Status
**PATCH** `/mentorships/{id}/status`

Update only the status of a mentorship.

**Request Body:**
```json
{
    "status": "completed"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Mentorship status updated successfully",
    "data": {
        "mentorship": {
            "id": 1,
            "member_id": 1,
            "mentor_id": 2,
            "start_date": "2024-01-01",
            "end_date": null,
            "status": "completed",
            "meeting_frequency": "weekly",
            "notes": "Regular mentorship",
            "completed_at": "2025-10-21T23:38:47.000000Z",
            "created_at": "2025-10-21T23:38:47.000000Z",
            "updated_at": "2025-10-21T23:38:47.000000Z"
        }
    }
}
```

#### Delete Mentorship
**DELETE** `/mentorships/{id}`

Delete a mentorship record.

**Response:**
```json
{
    "success": true,
    "message": "Mentorship deleted successfully"
}
```

### Messages

#### List Messages
**GET** `/messages`

Get a paginated list of all messages.

#### Create Message
**POST** `/messages`

Create a new email message.

**Request Body:**
```json
{
    "message_type": "general",
    "channel": "email",
    "subject": "Test Subject",
    "content": "Message content",
    "recipients": ["all_members"],
    "schedule_type": "immediate"
}
```

#### Send Message Now
**POST** `/messages/{id}/send-now`

Send a draft message immediately.

### Reports

#### Get Attendance Trends
**GET** `/reports/attendance-trends?start_date=2025-01-01&end_date=2025-01-31`

Get attendance trends over a specified period.

#### Get Member Engagement
**GET** `/reports/member-engagement`

Get member engagement analytics including engagement rates and top engaged members.

#### Get Class Performance
**GET** `/reports/class-performance`

Get class performance metrics including attendance rates and top performing classes.

#### Get Mentorship Success
**GET** `/reports/mentorship-success`

Get mentorship success tracking including completion rates and average duration.

### Dashboard

#### Get Dashboard Summary
**GET** `/dashboard/summary`

Get summary statistics for the dashboard.

**Response:**
```json
{
    "success": true,
    "data": {
        "total_members": 50,
        "total_classes": 4,
        "total_sessions": 20,
        "total_mentorships": 15,
        "active_classes": 3,
        "upcoming_sessions": 5,
        "recent_members": [
            {
                "id": 1,
                "full_name": "John Doe",
                "phone": "0712345678",
                "date_of_conversion": "2024-01-01",
                "created_at": "2025-10-21T23:38:47.000000Z"
            }
        ],
        "recent_classes": [
            {
                "id": 1,
                "title": "New Believers Class",
                "mentor": {
                    "name": "Pastor John Smith"
                },
                "created_at": "2025-10-21T23:38:47.000000Z"
            }
        ]
    }
}
```

## Error Responses

All API endpoints return consistent error responses:

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Authentication Error (401)
```json
{
    "success": false,
    "message": "Unauthenticated."
}
```

### Authorization Error (403)
```json
{
    "success": false,
    "message": "Access denied. Admin privileges required."
}
```

### Not Found Error (404)
```json
{
    "success": false,
    "message": "Resource not found."
}
```

### Server Error (500)
```json
{
    "success": false,
    "message": "Internal server error."
}
```

## Rate Limiting

The API implements rate limiting to prevent abuse:

- **Authentication endpoints**: 5 requests per minute
- **Other endpoints**: 60 requests per minute

Rate limit headers are included in responses:
```
X-RateLimit-Limit: 60
X-RateLimit-Remaining: 59
X-RateLimit-Reset: 1640995200
```

## Testing

### Demo Credentials

For testing purposes, use these demo credentials:

- **Admin**: `admin@discipleship.local` / `password`
- **Pastor**: `pastor@discipleship.local` / `password`
- **Coordinator**: `coordinator@discipleship.local` / `password`

### Example cURL Commands

#### Login
```bash
curl -X POST http://localhost:8000/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{
    "email": "admin@discipleship.local",
    "password": "password"
  }'
```

#### Get Members
```bash
curl -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  http://localhost:8000/api/v1/members
```

#### Create Member
```bash
curl -X POST http://localhost:8000/api/v1/members \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE" \
  -d '{
    "full_name": "John Doe",
    "phone": "0712345678",
    "email": "john@example.com",
    "date_of_conversion": "2024-01-01",
    "preferred_contact": "sms",
    "notes": "New member"
  }'
```

## Support

For API support or questions, please contact the development team or refer to the application documentation.
