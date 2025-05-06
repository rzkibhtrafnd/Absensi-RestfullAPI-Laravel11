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
  "status": "success",
  "message": "Login successful",
  "data": {
    "user": {
      "id": 1,
      "name": "User Name",
      "email": "user@example.com",
      "role": "admin"
    },
    "token": "your-access-token"
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
  "status": "success",
  "message": "Logged out successfully"
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
  "status": "success",
  "data": {
    "qr_code": "encoded-qr-data",
    "expires_at": "2023-06-01T12:00:00Z"
  }
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
  "status": "success",
  "data": {
    "employees": [
      {
        "id": 1,
        "name": "Employee Name",
        "email": "employee@example.com",
        "role": "pegawai",
        "created_at": "2023-06-01T12:00:00Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 50,
      "last_page": 4
    }
  }
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
  "name": "New Employee",
  "email": "newemployee@example.com",
  "password": "password123",
  "role": "pegawai"
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Employee created successfully",
  "data": {
    "id": 2,
    "name": "New Employee",
    "email": "newemployee@example.com",
    "role": "pegawai",
    "created_at": "2023-06-01T12:00:00Z"
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
  "status": "success",
  "data": {
    "id": 1,
    "name": "Employee Name",
    "email": "employee@example.com",
    "role": "pegawai",
    "created_at": "2023-06-01T12:00:00Z"
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
  "name": "Updated Name",
  "email": "updated@example.com",
  "role": "hr"
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Employee updated successfully",
  "data": {
    "id": 1,
    "name": "Updated Name",
    "email": "updated@example.com",
    "role": "hr",
    "updated_at": "2023-06-02T12:00:00Z"
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
  "status": "success",
  "message": "Employee deleted successfully"
}
```

## Search Employees
Search for employees by name or email.

- URL: /api/admin/pegawai/search
- Method: GET
- Auth Required: Yes (Admin role)
- Headers: Authorization: Bearer your-token
- Query Parameters:
    - query: Search term
    - page: Page number (default: 1)
    - per_page: Items per page (default: 15)
- Success Response:
```json
{
  "status": "success",
  "data": {
    "employees": [
      {
        "id": 1,
        "name": "Employee Name",
        "email": "employee@example.com",
        "role": "pegawai"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 5,
      "last_page": 1
    }
  }
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
    - page: Page number (default: 1)
    - per_page: Items per page (default: 15)
- Success Response:
```json
{
  "status": "success",
  "data": {
    "employees": [
      {
        "id": 1,
        "name": "Employee Name",
        "email": "employee@example.com",
        "role": "hr"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 10,
      "last_page": 1
    }
  }
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
  "status": "success",
  "data": {
    "work_start_time": "08:00:00",
    "work_end_time": "17:00:00",
    "qr_refresh_interval": 30,
    "late_threshold_minutes": 15,
    "work_days": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]
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
  "work_start_time": "09:00:00",
  "work_end_time": "18:00:00",
  "qr_refresh_interval": 60,
  "late_threshold_minutes": 10,
  "work_days": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Settings updated successfully",
  "data": {
    "work_start_time": "09:00:00",
    "work_end_time": "18:00:00",
    "qr_refresh_interval": 60,
    "late_threshold_minutes": 10,
    "work_days": ["Monday", "Tuesday", "Wednesday", "Thursday", "Friday"]
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
- Query Parameters:
```json
- start_date: Filter by start date (YYYY-MM-DD)
- end_date: Filter by end date (YYYY-MM-DD)
- employee_id: Filter by employee ID
- page: Page number (default: 1)
- per_page: Items per page (default: 15)
```
- Success Response:
```json
{
  "status": "success",
  "data": {
    "records": [
      {
        "id": 1,
        "employee_id": 1,
        "employee_name": "Employee Name",
        "check_in": "2023-06-01T08:05:20Z",
        "check_out": "2023-06-01T17:02:15Z",
        "status": "on_time",
        "created_at": "2023-06-01T08:05:20Z"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 100,
      "last_page": 7
    }
  }
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
  "qr_data": "encoded-qr-data",
  "location": {
    "latitude": 37.7749,
    "longitude": -122.4194
  }
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Attendance recorded successfully",
  "data": {
    "type": "check_in",
    "time": "2023-06-01T08:05:20Z",
    "status": "on_time"
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
- Request Body:
```json
{
  "date": "2023-06-01",
  "check_in": "08:00:00",
  "check_out": "17:00:00",
  "alasan": "Forgot to scan QR",
  "lampiran": "base64-encoded-image" // Optional
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Attendance request submitted successfully",
  "data": {
    "id": 1,
    "date": "2023-06-01",
    "check_in": "08:00:00",
    "check_out": "17:00:00",
    "status": "pending",
    "created_at": "2023-06-02T10:15:30Z"
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
- Query Parameters:
```json
start_date: Filter by start date (YYYY-MM-DD)
end_date: Filter by end date (YYYY-MM-DD)
page: Page number (default: 1)
per_page: Items per page (default: 15)
```
- Success Response:
```json
{
  "status": "success",
  "data": {
    "records": [
      {
        "id": 1,
        "date": "2023-06-01",
        "check_in": "08:05:20",
        "check_out": "17:02:15",
        "status": "on_time",
        "working_hours": "08:57:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 30,
      "last_page": 2
    }
  }
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

- Request Body:
```json
{
  "notes": "Approved based on evidence provided" // Optional
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Attendance request approved",
  "data": {
    "id": 1,
    "status": "approved",
    "updated_at": "2023-06-03T09:10:15Z"
  }
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
```json{
  "keterangan_approval": "Insufficient evidence provided" // Optional
}
```

- Success Response:
```json
{
  "status": "success",
  "message": "Attendance request rejected",
  "data": {
    "id": 1,
    "status": "rejected",
    "updated_at": "2023-06-03T09:15:20Z"
  }
}
```

## Get Request History
Get history of attendance requests.

- URL: /api/hr/absensi/riwayat-pengajuan
- Method: GET
- Auth Required: Yes (HR role)
- Headers: Authorization: Bearer your-token
- Query Parameters:
```json
status: Filter by status (pending, approved, rejected)
start_date: Filter by start date (YYYY-MM-DD)
end_date: Filter by end date (YYYY-MM-DD)
employee_id: Filter by employee ID
page: Page number (default: 1)
per_page: Items per page (default: 15)
```

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

## Get Attendance Settings (HR)
Get current attendance system settings.

- URL: /api/hr/absensi/settings
- Method: GET
- Auth Required: Yes (HR role)
```json
Headers: Authorization: Bearer your-token
Success Response: Same as admin endpoint
```

## Update Attendance Settings (HR)
Update attendance system settings.

- URL: /api/hr/absensi/settings
- Method: PUT
- Auth Required: Yes (HR role)
- Headers:
```json
Authorization: Bearer your-token
Content-Type: application/json
```

- Request Body: Same as admin endpoint
- Success Response: Same as admin endpoint

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
  "qr_data": "encoded-qr-data",
  "location": {
    "latitude": 37.7749,
    "longitude": -122.4194
  }
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Attendance recorded successfully",
  "data": {
    "type": "check_in",
    "time": "2023-06-01T08:05:20Z",
    "status": "on_time"
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

- Request Body:
```json
{
  "date": "2023-06-01",
  "check_in": "08:00:00",
  "check_out": "17:00:00",
  "reason": "Forgot to scan QR",
  "evidence": "base64-encoded-image" // Optional
}
```
- Success Response:
```json
{
  "status": "success",
  "message": "Attendance request submitted successfully",
  "data": {
    "id": 1,
    "date": "2023-06-01",
    "check_in": "08:00:00",
    "check_out": "17:00:00",
    "status": "pending",
    "created_at": "2023-06-02T10:15:30Z"
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
- Query Parameters:
```json
start_date: Filter by start date (YYYY-MM-DD)
end_date: Filter by end date (YYYY-MM-DD)
page: Page number (default: 1)
per_page: Items per page (default: 15)
```

- Success Response:
```json{
  "status": "success",
  "data": {
    "records": [
      {
        "id": 1,
        "date": "2023-06-01",
        "check_in": "08:05:20",
        "check_out": "17:02:15",
        "status": "on_time",
        "working_hours": "08:57:00"
      }
    ],
    "pagination": {
      "current_page": 1,
      "per_page": 15,
      "total": 30,
      "last_page": 2
    }
  }
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
- Query Parameters:
```json
status: Filter by status (pending, approved, rejected)
start_date: Filter by start date (YYYY-MM-DD)
end_date: Filter by end date (YYYY-MM-DD)
page: Page number (default: 1)
per_page: Items per page (default: 15)
```

- Success Response:
```json{
  "status": "success",
  "data": {
    "requests": [
      {
        "id": 1,
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
      "total": 5,
      "last_page": 1
    }
  }
}
```

## Error Responses
Validation Error
```json{
  "status": "error",
  "message": "Validation failed",
  "errors": {
    "email": ["The email field is required."],
    "password": ["The password field is required."]
  }
}
```
Authentication Error
```json{
  "status": "error",
  "message": "Unauthenticated."
}
```
Authorization Error
```json{
  "status": "error",
  "message": "You do not have permission to access this resource."
}
```
Resource Not Found
```json{
  "status": "error",
  "message": "Resource not found."
}
```
Server Error
```json{
  "status": "error",
  "message": "An unexpected error occurred."
}
```
