# Student Management API

A simple **PHP REST API** for managing students, including CRUD operations, fee status management, and email notifications.

---

## Base URL

```
https://api.istudyglobe.com
```

All endpoints are relative to the base URL.

---

## Table of Contents

- [Endpoints](#-endpoints)
  - [Get All Students](#1-get-all-students-with-optional-filters)
  - [Get Student by ID](#2-get-student-by-id)
  - [Create Student](#3-create-student)
  - [Update Student](#4-update-student)
  - [Delete Student](#5-delete-student)
  - [Update Fee Status](#6-update-fee-status)
  - [Search Students by Name](#7-search-students-by-name)
- [Setup Guide](#-setup-guide)
- [Error Handling](#-error-handling)

---

## Endpoints

### 1. Get All Students (with optional filters)

```
GET /students
```

**Query Parameters (optional):**

| Parameter  | Description                    | Example |
| ---------- | ------------------------------ | ------- |
| fee_status | Filter by fee status           | unpaid  |
| class      | Filter by class (e.g., ND,HND) | ND      |

**Example Request:**

```http
GET /students?fee_status=unpaid&class=ND
```

**Response:**

```json
[
  {
    "id": 3,
    "reg_number": "003",
    "first_name": "Muhammad",
    "last_name": "Mustapha",
    "email": "nuru@example.com",
    "class": "ND1",
    "fee_status": "unpaid",
    "created_at": "2025-08-28 14:00:00"
  }
]
```

---

### 2. Get Student by ID

```
GET /students/{id}
```

**Response:**

```json
{
  "id": 3,
  "reg_number": "003",
  "first_name": "Muhammad",
  "last_name": "Mustapha",
  "email": "nuru@example.com",
  "class": "ND1",
  "fee_status": "unpaid",
  "created_at": "2025-08-28 14:00:00"
}
```

---

### 3. Create Student

```
POST /students
```

**Body (JSON):**

```json
{
  "reg_number": "004",
  "first_name": "Ali",
  "last_name": "Umar",
  "email": "ali@example.com",
  "class": "ND1"
}
```

**Response:**

```json
{
  "id": 4,
  "reg_number": "004",
  "first_name": "Ali",
  "last_name": "Umar",
  "email": "ali@example.com",
  "class": "ND1",
  "fee_status": "unpaid",
  "created_at": "2025-08-28 15:00:00",
  "email_status": "sent"
}
```

> **Note:** `email_status` shows whether the welcome email was successfully sent.

---

### 4. Update Student

```
PUT /students/{id}
```

**Body (JSON):**

> `reg_number` and `email` cannot be changed.

```json
{
  "first_name": "Muhammad",
  "last_name": "Mustapha",
  "class": "HND"
}
```

**Response:**

```json
{
  "id": 3,
  "reg_number": "003",
  "first_name": "Muhammad",
  "last_name": "Mustapha",
  "email": "nuru@example.com",
  "class": "HND",
  "fee_status": "unpaid",
  "created_at": "2025-08-28 14:00:00"
}
```

---

### 5. Delete Student

```
DELETE /students/{id}
```

**Response:**

```json
{
  "message": "Student deleted successfully"
}
```

---

### 6. Update Fee Status

```
PUT /students/{id}/fee
```

**Body (JSON):**

```json
{
  "fee_status": "paid"
}
```

**Response:**

```json
{
  "id": 3,
  "reg_number": "003",
  "first_name": "Muhammad",
  "last_name": "Mustapha",
  "email": "nuru@example.com",
  "class": "ND1",
  "fee_status": "paid",
  "created_at": "2025-08-28 14:00:00"
}
```

> **Note:** Only `fee_status` is allowed. Extra fields will return an error.

---

### 7. Search Students by Name

```
GET /students/search?name=Muhammad
```

**Response:**

```json
[
  {
    "id": 4,
    "reg_number": "004",
    "first_name": "Muhammad",
    "last_name": "Mustapha",
    "email": "nuru@example.com",
    "class": "HND",
    "fee_status": "paid",
    "created_at": "2025-08-28 15:00:00"
  }
]
```

---

## Setup Guide

### 1. Clone the Repository

```bash
git clone https://github.com/ElderNurudeen/sms.git
cd sms-api
```

### 2. Environment Configuration

Create a `.env` file inside `config/` directory:

```ini
# Database Configuration
DB_HOST=localhost
DB_NAME=your_database
DB_USER=root
DB_PASS=secret

# SMTP Configuration for Email Notifications
SMTP_HOST=smtp.example.com
SMTP_PORT=587
SMTP_USERNAME=your_email@example.com
SMTP_PASSWORD=your_password
SMTP_SECURE=tls
SMTP_FROM_EMAIL=admin@example.com
SMTP_FROM_NAME="School Admin"
```

### 3. Database Setup

Run the following SQL to create the `students` table:

```sql
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    reg_number VARCHAR(50) UNIQUE NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    class VARCHAR(50) NOT NULL,
    fee_status ENUM('paid','unpaid') DEFAULT 'unpaid',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

---

## Error Handling

All errors return JSON with an `error` field.

**Example:**

```json
{
  "error": "Student not found"
}
```

**Common Status Codes:**

- `200` - Success
- `201` - Created
- `400` - Bad Request
- `404` - Not Found
- `500` - Internal Server Error

**Example Error Responses:**

```json
{
  "error": "Email cannot be changed"
}
```

```json
{
  "error": "Student not found"
}
```
