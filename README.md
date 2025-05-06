# Attendance System RESTful API - Laravel 11

## Overview
This project is a RESTful API built with Laravel 11 for an employee attendance management system. The system allows employees to record their attendance via QR scanning, submit attendance requests, and view their attendance history. HR and admin users have additional privileges for managing employees and attendance settings.

## Features
- Authentication: Secure login and token-based authentication using Laravel Sanctum
- Role-based Access Control: Different permissions for admin, HR, and regular employees
- QR-based Attendance: Modern attendance tracking through QR code scanning
- Attendance Management: Submit, approve, and reject attendance requests
- Employee Management: CRUD operations for employee data with search and filter capabilities
- Attendance Settings: Configurable settings for the attendance system

## Technologies Used
- PHP 8.X
- Laravel 11
- Laravel Sanctum for Authentication
- MySQL/PostgreSQL (database)
- RESTful API architecture

## System Requirements
- PHP >= 8.1
- Composer
- MySQL/PostgreSQL
- Web server (Apache/Nginx)

---

# Installation
## Step 1: Clone the Repository

First, clone the repository from GitHub:

```bash
git clone https://github.com/rzkibhtrafnd/Absensi-RestfullAPI-Laravel11.git
cd Absensi-RestfullAPI-Laravel11
```

## Step 2: Install PHP Dependencies
Install the required PHP packages using Composer:

```bash
composer install
```

## Step 3: Environment Configuration
Copy the example environment file and configure it for your system:

```bash
cp .env.example .env
```

Now, edit the .env file and update the database connection settings:

```bash
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=booking_api_db
DB_USERNAME=your_db_username
DB_PASSWORD=your_db_password
```

Also, configure any other environment-specific settings like mail server, app URL, etc.

## Step 4: Generate Application Key
Generate a unique application key:

```bash
php artisan key:generate
```

## Step 5: Run Migrations and Seeders
Run the migrations to create tables in your database:

```bash
php artisan migrate
```

Optionally, seed the database with initial data:

```bash
php artisan db:seed
```

## Step 6: Link Storage
Link the storage directory to make uploaded files accessible from the web:

```bash
php artisan storage:link
```

## Step 7: Set up Laravel Sanctum

```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

## Step 8: Start the Development Server
Start Laravel's built-in development server:

```bash
php artisan serve
```

The API will now be accessible at:

```bash
http://localhost:8000
```

---

## User Roles
1. Admin: Has full access to all features including employee management and attendance settings
2. HR: Can manage employees and approve/reject attendance requests
3. Employee: Can scan QR codes for attendance and submit attendance requests

## Contributing
1. Fork the repository
2. Create a new branch (git checkout -b feature/your-feature)
3. Commit your changes (git commit -m 'Add some feature')
4. Push to the branch (git push origin feature/your-feature)
5. Open a pull request

---

# API Documentation
## Authentication
## Login
Authenticates a user and returns an access token.
- URL: /api/login
- Method: POST
- Auth Required: No
- Request Body:

```json
{
  "email": "user@example.com",
  "password": "yourpassword"
}
```
- Succes Response:
```json
{
    "message": "Login berhasil",
    "token": "1|MhUcBWC06Cf4dd8ZXO4OYDXY*************",
    "user": {
        "id": 1,
        "name": "Admin User",
        "email": "admin@mail.com",
        "email_verified_at": null,
        "role": "admin",
        "divisi": null,
        "posisi": null,
        "created_at": "2025-05-06T00:51:27.000000Z",
        "updated_at": "2025-05-06T00:51:27.000000Z"
    }
}
```

## Logout
Invalidates the current access token.
- URL: /api/logout
- Method: POST
- Auth Required: Yes
- Headers: Authorization: Bearer your-token
- Success Response:
```json
{
    "message": "Logout berhasil"
}
```

## QR Code
Get Latest QR
Retrieves the latest QR code for attendance.
- URL: /api/qr-latest
- Method: GET
- Auth Required: No
- Success Response:
```json
{
    "success": true,
    "qr_image": "data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0iVVRGLTgiPz4KPHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZlcnNpb249IjEuMSIgd2lkdGg9IjMwMCIgaGVpZ2h0PSIzMDAiIHZpZXdCb3g9IjAgMCAzMDAgMzAwIj48cmVjdCB4PSIwIiB5PSIwIiB3aWR0aD0******************",
    "token": "fp1gbWzoO3SXf**************",
    "expired_at": "2025-05-06 11:00:00"
}
```

## Admin Routes
## Admin Dashboard
Get admin dashboard information.

- URL: /api/admin/dashboard
- Method: GET
- Auth Required: Yes (Admin role)
- Headers: Authorization: Bearer your-token
- Success Response
```json
{
  "message": "Admin dashboard"
}
```

## Employee Management (Admin)
## List All Employees
Get a paginated list of all employees.

- URL: /api/admin/pegawai
- Method: GET
- Auth Required: Yes (Admin role)
- Headers: Authorization: Bearer your-token
- Query Parameters:
  1. page: Page number (default: 1)
  2. per_page: Items per page (default: 15)
- Success Response:
```json
{
    "success": true,
    "message": "",
    "data": [
        {
            "id": 2,
            "name": "Kepala HRD",
            "email": "hr@mail.com",
            "email_verified_at": null,
            "role": "hr",
            "divisi": "HRD",
            "posisi": "Kepala HRD",
            "created_at": "2025-05-06T00:51:27.000000Z",
            "updated_at": "2025-05-06T00:51:27.000000Z"
        },
        {
            "id": 3,
            "name": "Pegawai User",
            "email": "pegawai@mail.com",
            "email_verified_at": null,
            "role": "pegawai",
            "divisi": "IT",
            "posisi": "Software Engineer",
            "created_at": "2025-05-06T00:51:27.000000Z",
            "updated_at": "2025-05-06T00:51:27.000000Z"
        }
    ]
}
```

## Create Employee
Create a new employee.

- URL: /api/admin/pegawai
- Method: POST
- Auth Required: Yes (Admin role)
- Headers: Authorization: Bearer your-token
- Request Body:
```json
{
  "name": "Budi Santoso",
  "email": "budi@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "pegawai",
  "divisi": "IT",
  "posisi": "Developer"
}
```
- Success Response:
```json
{
    "success": true,
    "message": "Pegawai berhasil ditambahkan",
    "data": {
        "name": "Budi Santoso",
        "email": "budi@example.com",
        "role": "pegawai",
        "divisi": "IT",
        "posisi": "Developer",
        "updated_at": "2025-05-06T01:05:39.000000Z",
        "created_at": "2025-05-06T01:05:39.000000Z",
        "id": 14
    }
}
```

## Get Employee Details
Get details of a specific employee.

- URL: /api/admin/pegawai/{id}
- Method: GET
- Auth Required: Yes (Admin role)
- Headers: Authorization: Bearer your-token
- Success Response:
```json
{
    "success": true,
    "message": "",
    "data": {
        "id": 14,
        "name": "Budi Santoso",
        "email": "budi@example.com",
        "email_verified_at": null,
        "role": "pegawai",
        "divisi": "IT",
        "posisi": "Developer",
        "created_at": "2025-05-06T01:05:39.000000Z",
        "updated_at": "2025-05-06T01:05:39.000000Z"
    }
}
```

## Update Employee
Update an existing employee.

- URL: /api/admin/pegawai/{id}
- Method: PUT
- Auth Required: Yes (Admin role)
- Headers:

```json
Authorization: Bearer your-token
Content-Type: application/json
```

Request Body:
```json
{
  "name": "Budi Santoso Update",
  "email": "budiupdate@example.com",
  "role": "pegawai",
  "divisi": "IT",
  "posisi": "Lead Developer"
}
```
- Success Response:
```json
{
    "success": true,
    "message": "Pegawai berhasil diperbarui",
    "data": {
        "id": 14,
        "name": "Budi Santoso Update",
        "email": "budiupdate@example.com",
        "email_verified_at": null,
        "role": "pegawai",
        "divisi": "IT",
        "posisi": "Lead Developer",
        "created_at": "2025-05-06T01:05:39.000000Z",
        "updated_at": "2025-05-06T01:07:10.000000Z"
    }
}
```

## Delete Employee
Delete an employee.

- URL: /api/admin/pegawai/{id}
- Method: DELETE
- Auth Required: Yes (Admin role)
- Headers:
```json
Authorization: Bearer your-token
```

- Success Response:
```json
{
    "success": true,
    "message": "Pegawai berhasil dihapus",
    "data": null
}
```

## Search Employees
Search for employees by name or email.

- URL: /api/admin/pegawai/search
- Method: GET
- Auth Required: Yes (Admin role)
- Headers: Authorization: Bearer your-token
- Query Parameters:
    - query: Pearlie
- Success Response:
```json
{
    "success": true,
    "message": "",
    "data": [
        {
            "id": 5,
            "name": "Pearlie Sporer",
            "email": "alayna08@example.org",
            "email_verified_at": "2025-05-06T00:51:27.000000Z",
            "role": "pegawai",
            "divisi": "HRD",
            "posisi": "Sales Person",
            "created_at": "2025-05-06T00:51:27.000000Z",
            "updated_at": "2025-05-06T00:51:27.000000Z"
        }
    ]
}
```

## Filter Employees by Role
Filter employees by their role.

- URL: /api/admin/pegawai/filter/role
- Method: GET
- Auth Required: Yes (Admin role)
- Headers: Authorization: Bearer your-token
- Query Parameters:
    - role: Role to filter by (admin, hr, pegawai)
- Success Response:
```json
{
    "success": true,
    "message": "",
    "data": [
        {
            "id": 2,
            "name": "Kepala HRD",
            "email": "hr@mail.com",
            "email_verified_at": null,
            "role": "hr",
            "divisi": "HRD",
            "posisi": "Kepala HRD",
            "created_at": "2025-05-06T00:51:27.000000Z",
            "updated_at": "2025-05-06T00:51:27.000000Z"
        }
    ]
}
```

## Attendance Settings (Admin)
## Get Attendance Settings
Get current attendance system settings.

- URL: /api/admin/absensi/settings
- Method: GET
- Auth Required: Yes (Admin role)
- Headers:
  ```json
  Authorization: Bearer your-token
  ```
- Success Response:
```json
{
    "success": true,
    "settings": {
        "id": 1,
        "check_in_start": "07:00:00",
        "check_in_end": "08:00:00",
        "check_out_time": "16:00:00",
        "radius_meters": 50,
        "late_tolerance": "08:15:00",
        "office_address": "Jl. Merdeka No.1, Jakarta",
        "office_latitude": "-6.2000000",
        "office_longitude": "106.8166660",
        "created_at": "2025-05-06T01:16:37.000000Z",
        "updated_at": "2025-05-06T01:16:37.000000Z"
    }
}
```

## Update Attendance Settings
Update attendance system settings.

- URL: /api/admin/absensi/settings
- Method: PUT
- Auth Required: Yes (Admin role)
- Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```
- Request Body:
```json
{
  "check_in_start": "07:00",
  "check_in_end": "08:00",
  "check_out_time": "16:00",
  "radius_meters": 50,
  "late_tolerance": "08:15",
  "office_address": "Jl. Merdeka No.1, Jakarta",
  "office_latitude": -6.200000,
  "office_longitude": 106.816666
}
```
- Success Response:
```json
{
    "success": true,
    "message": "Pengaturan absensi berhasil diperbarui.",
    "settings": {
        "check_in_start": "07:00",
        "check_in_end": "08:00",
        "check_out_time": "16:00",
        "radius_meters": 50,
        "late_tolerance": "08:15",
        "office_address": "Jl. Merdeka No.1, Jakarta",
        "office_latitude": -6.2,
        "office_longitude": 106.816666,
        "updated_at": "2025-05-06T01:16:37.000000Z",
        "created_at": "2025-05-06T01:16:37.000000Z",
        "id": 1
    }
}
```

## HR Routes
## Employee Management (HR)
HR has the same employee management endpoints as Admin:

- GET /api/hr/pegawai - List all employees
- POST /api/hr/pegawai - Create an employee
- GET /api/hr/pegawai/{id} - Get employee details
- PUT /api/hr/pegawai/{id} - Update an employee
- DELETE /api/hr/pegawai/{id} - Delete an employee
- GET /api/hr/pegawai/search - Search employees
- GET /api/hr/pegawai/filter/role - Filter employees by role

The request and response formats are identical to the Admin endpoints.

## Attendance Management (HR)
## List All Attendance Records
Get a paginated list of all attendance records.

- URL: /api/hr/absensi
- Method: GET
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
```

- Success Response:
```json
{
    "success": true,
    "semua_pegawai": [
        {
            "id": 1,
            "pegawai_id": 3,
            "tanggal": "2025-05-06",
            "jam_masuk": "08:46:09",
            "jam_keluar": "08:46:33",
            "status": "Terlambat",
            "alasan": null,
            "lampiran": null,
            "created_at": "2025-05-06T01:46:09.000000Z",
            "updated_at": "2025-05-06T01:46:33.000000Z",
            "approval_status": "approved",
            "approved_by": null,
            "keterangan_approval": null,
            "pegawai": {
                "id": 3,
                "name": "Pegawai User",
                "email": "pegawai@mail.com",
                "email_verified_at": null,
                "role": "pegawai",
                "divisi": "IT",
                "posisi": "Software Engineer",
                "created_at": "2025-05-06T00:51:27.000000Z",
                "updated_at": "2025-05-06T00:51:27.000000Z"
            }
        },
        {
            "id": 2,
            "pegawai_id": 5,
            "tanggal": "2025-05-06",
            "jam_masuk": null,
            "jam_keluar": null,
            "status": "Izin",
            "alasan": "Menghadiri acara keluarga",
            "lampiran": "absensi_5_20250506.pdf",
            "created_at": "2025-05-06T02:16:43.000000Z",
            "updated_at": "2025-05-06T02:16:43.000000Z",
            "approval_status": "pending",
            "approved_by": null,
            "keterangan_approval": null,
            "pegawai": {
                "id": 5,
                "name": "Pearlie Sporer",
                "email": "alayna08@example.org",
                "email_verified_at": "2025-05-06T00:51:27.000000Z",
                "role": "pegawai",
                "divisi": "HRD",
                "posisi": "Sales Person",
                "created_at": "2025-05-06T00:51:27.000000Z",
                "updated_at": "2025-05-06T00:51:27.000000Z"
            }
        }
    ],
    "riwayat_pribadi": []
}
```

## Scan QR for Attendance
Record attendance by scanning QR code.

- URL: /api/hr/absensi/scan-qr
- Method: POST
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```
- Request Body:
```json
{
  "token": "abc123xyz",
  "latitude": -6.2001,
  "longitude": 106.8167
}

```
- Success Response(check-in):
```json
{
    "success": true,
    "message": "Check-in berhasil tapi Anda terlambat",
    "type": "checkin",
    "absensi": {
        "pegawai_id": 3,
        "tanggal": "2025-05-06",
        "jam_masuk": "08:46:09",
        "status": "Terlambat",
        "approval_status": "approved",
        "updated_at": "2025-05-06T01:46:09.000000Z",
        "created_at": "2025-05-06T01:46:09.000000Z",
        "id": 1
    }
}
```
- Success Response(check-out):
```json
{
    "success": true,
    "message": "Check-out berhasil",
    "type": "checkout",
    "absensi": {
        "id": 1,
        "pegawai_id": 3,
        "tanggal": "2025-05-06",
        "jam_masuk": "08:46:09",
        "jam_keluar": "08:46:33",
        "status": "Terlambat",
        "alasan": null,
        "lampiran": null,
        "created_at": "2025-05-06T01:46:09.000000Z",
        "updated_at": "2025-05-06T01:46:33.000000Z",
        "approval_status": "approved",
        "approved_by": null,
        "keterangan_approval": null
    }
}
```

## Submit Attendance Request
Submit a manual attendance request.

- URL: /api/hr/absensi/ajukan
- Method: POST
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```
- Request Body(form-data):
    - tanggal: 2025-05-06
    - status: Izin
    - alasan: Menghadiri acara keluarga
    - lampiran: [Upload file PDF/JPG/PNG]

- Success Response:
```json
{
    "success": true,
    "message": "Pengajuan absensi berhasil dikirim",
    "absensi": {
        "pegawai_id": 5,
        "tanggal": "2025-05-06",
        "status": "Izin",
        "alasan": "Menghadiri acara keluarga",
        "lampiran": "absensi_5_20250506.pdf",
        "approved_by": null,
        "approval_status": "pending",
        "updated_at": "2025-05-06T02:16:43.000000Z",
        "created_at": "2025-05-06T02:16:43.000000Z",
        "id": 2
    }
}
```

## Get Attendance History
Get attendance history for the authenticated user.

- URL: /api/hr/absensi/riwayat
- Method: GET
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
```
- Success Response:
```json
{
    "success": true,
    "message": "Riwayat absensi ditemukan",
    "riwayat": []
}
```
## Get Attendance History Request
Get attendance history for the employee request
- URL: /api/hr/absensi/riwayat-pengajuan
- Method: GET
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
```
- Success Response:
```json
{
    "success": true,
    "message": "Riwayat pengajuan absensi ditemukan.",
    "pengajuan": [
        {
            "id": 2,
            "pegawai_id": 5,
            "tanggal": "2025-05-06",
            "jam_masuk": null,
            "jam_keluar": null,
            "status": "Izin",
            "alasan": "Menghadiri acara keluarga",
            "lampiran": "absensi_5_20250506.pdf",
            "created_at": "2025-05-06T02:16:43.000000Z",
            "updated_at": "2025-05-06T02:16:43.000000Z",
            "approval_status": "pending",
            "approved_by": null,
            "keterangan_approval": null
        }
    ]
}
```

## Approve Attendance Request
Approve a pending attendance request.

- URL: /api/hr/absensi/{id}/approve
- Method: POST
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```
- Success Response:
```json
{
  "message": "Pengajuan absensi disetujui."
}
```

## Reject Attendance Request
Reject a pending attendance request.

- URL: /api/hr/absensi/{id}/reject
- Method: POST
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```

- Request Body:
```json
{
  "alasan_ditolak": "Lampiran tidak valid"
}
```

- Success Response:
```json
{
  "message": "Pengajuan absensi ditolak."
}
```

## Get Request History
Get history of attendance requests.

- URL: /api/hr/absensi/riwayat-pengajuan
- Method: GET
- Auth Required: Yes (HR role)
- Headers: Authorization: Bearer your-token
- Success Response:
```json
{
  "status": "success",
  "data": {
    "requests": [
      {
        "id": 1,
        "employee_id": 1,
        "employee_name": "Employee Name",
        "date": "2023-06-01",
        "check_in": "08:00:00",
        "check_out": "17:00:00",
        "reason": "Forgot to scan QR",
        "status": "approved",
        "notes": "Approved based on evidence provided",
        "created_at": "2023-06-02T10:15:30Z",
        "updated_at": "2023-06-03T09:10:15Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 25,
      "last_page": 2
    }
  }
}
```


## Employee Routes
## Attendance Management (Employee)
## Scan QR for Attendance
Record attendance by scanning QR code.

- URL: /api/pegawai/absensi/scan-qr
- Method: POST
- Auth Required: Yes (Employee role)
Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```

- Request Body:
```json
{
  "token": "abc123xyz",
  "latitude": -6.2001,
  "longitude": 106.8167
}

```
- Success Response(check-in):
```json
{
    "success": true,
    "message": "Check-in berhasil tapi Anda terlambat",
    "type": "checkin",
    "absensi": {
        "pegawai_id": 3,
        "tanggal": "2025-05-06",
        "jam_masuk": "08:46:09",
        "status": "Terlambat",
        "approval_status": "approved",
        "updated_at": "2025-05-06T01:46:09.000000Z",
        "created_at": "2025-05-06T01:46:09.000000Z",
        "id": 1
    }
}
```
- Success Response(check-out):
```json
{
    "success": true,
    "message": "Check-out berhasil",
    "type": "checkout",
    "absensi": {
        "id": 1,
        "pegawai_id": 3,
        "tanggal": "2025-05-06",
        "jam_masuk": "08:46:09",
        "jam_keluar": "08:46:33",
        "status": "Terlambat",
        "alasan": null,
        "lampiran": null,
        "created_at": "2025-05-06T01:46:09.000000Z",
        "updated_at": "2025-05-06T01:46:33.000000Z",
        "approval_status": "approved",
        "approved_by": null,
        "keterangan_approval": null
    }
}
```

## Submit Attendance Request
Submit a manual attendance request.

- URL: /api/pegawai/absensi/ajukan
- Method: POST
- Auth Required: Yes (Employee role)
- Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```
- Request Body(form-data):
    - tanggal: 2025-05-06
    - status: Izin
    - alasan: Menghadiri acara keluarga
    - lampiran: [Upload file PDF/JPG/PNG]

- Success Response:
```json
{
    "success": true,
    "message": "Pengajuan absensi berhasil dikirim",
    "absensi": {
        "pegawai_id": 5,
        "tanggal": "2025-05-06",
        "status": "Izin",
        "alasan": "Menghadiri acara keluarga",
        "lampiran": "absensi_5_20250506.pdf",
        "approved_by": null,
        "approval_status": "pending",
        "updated_at": "2025-05-06T02:16:43.000000Z",
        "created_at": "2025-05-06T02:16:43.000000Z",
        "id": 2
    }
}
```

## Get Attendance History
Get attendance history for the authenticated employee.

- URL: /api/pegawai/absensi/riwayat
- Method: GET
- Auth Required: Yes (Employee role)
- Headers:
```json
Authorization: Bearer your-token
```

- Success Response:
```json
{
    "success": true,
    "message": "Riwayat absensi ditemukan",
    "riwayat": [
        {
            "id": 2,
            "pegawai_id": 5,
            "tanggal": "2025-05-06",
            "jam_masuk": null,
            "jam_keluar": null,
            "status": "Izin",
            "alasan": "Menghadiri acara keluarga",
            "lampiran": "absensi_5_20250506.pdf",
            "created_at": "2025-05-06T02:16:43.000000Z",
            "updated_at": "2025-05-06T02:16:43.000000Z",
            "approval_status": "pending",
            "approved_by": null,
            "keterangan_approval": null
        }
    ]
}
```

## Get Request History
Get history of attendance requests for the authenticated employee.

- URL: /api/pegawai/absensi/riwayat-pengajuan
- Method: GET
- Auth Required: Yes (Employee role)
- Headers:
```json
Authorization: Bearer your-token
```
- Success Response:
```json
{
    "success": true,
    "message": "Riwayat pengajuan absensi ditemukan.",
    "pengajuan": [
        {
            "id": 2,
            "pegawai_id": 5,
            "tanggal": "2025-05-06",
            "jam_masuk": null,
            "jam_keluar": null,
            "status": "Izin",
            "alasan": "Menghadiri acara keluarga",
            "lampiran": "absensi_5_20250506.pdf",
            "created_at": "2025-05-06T02:16:43.000000Z",
            "updated_at": "2025-05-06T02:16:43.000000Z",
            "approval_status": "pending",
            "approved_by": null,
            "keterangan_approval": null
        }
    ]
}
```

## Error Responses
Validation Error
```json
{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```
Authentication Error
```json
{
  "status": "error",
  "message": "Unauthenticated."
}
```
Authorization Error
```json
{
  "status": "error",
  "message": "You do not have permission to access this resource."
}
```
Resource Not Found
```json
{
  "status": "error",
  "message": "Resource not found."
}
```
Server Error
```json
{
  "status": "error",
  "message": "An unexpected error occurred."
}
```
