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

json
```
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
## Get Latest QR
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
## Get admin dashboard information.

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
## Get a paginated list of all employees.

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

##Create Employee
##Create a new employee.

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
## Get details of a specific employee.

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
## Update an existing employee.

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
## Delete an employee.

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
## Search for employees by name or email.

URL: /api/admin/pegawai/search
Method: GET
Auth Required: Yes (Admin role)
Headers: Authorization: Bearer your-token
Query Parameters:

query: Search term
page: Page number (default: 1)
per_page: Items per page (default: 15)


Success Response:
json{
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


Filter Employees by Role
Filter employees by their role.

URL: /api/admin/pegawai/filter/role
Method: GET
Auth Required: Yes (Admin role)
Headers: Authorization: Bearer your-token
Query Parameters:

role: Role to filter by (admin, hr, pegawai)
page: Page number (default: 1)
per_page: Items per page (default: 15)


Success Response:
json{
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
### Admin Controller

#### 1. Get All Owners (GET /api/admin/owners)
Retrieve a list of all property owners.

**Request:**
```
GET /api/admin/owners
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Response (Success - 200):**
```json
{
  "data": [
    {
      "id": 2,
      "name": "Owner 1",
      "email": "owner1@example.com",
      "created_at": "2023-05-15T10:00:00.000000Z"
    },
    {
      "id": 3,
      "name": "Owner 2",
      "email": "owner2@example.com",
      "created_at": "2023-05-15T11:00:00.000000Z"
    }
  ]
}
```

#### 2. Create Owner (POST /api/admin/owners)
Create a new property owner.

**Request:**
```
POST /api/admin/owners
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: multipart/form-data

{
  "name": "New Owner",
  "email": "new.owner@example.com",
  "password": "ownerpassword123"
}
```

**Response (Success - 201):**
```json
{
  "message": "Pemilik berhasil dibuat.",
  "data": {
    "id": 4,
    "name": "New Owner",
    "email": "new.owner@example.com",
    "created_at": "2023-05-15T12:00:00.000000Z"
  }
}
```

#### 3. Get Owner Detail (GET /api/admin/owners/{id})
Get detailed information about a specific owner.

**Request:**
```
GET /api/admin/owners/2
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Response (Success - 200):**
```json
{
  "data": {
    "id": 2,
    "name": "Owner 1",
    "email": "owner1@example.com",
    "created_at": "2023-05-15T10:00:00.000000Z"
  }
}
```

#### 4. Update Owner (PUT /api/admin/owners/{id})
Update details of an existing owner.

**Request:**
```
PUT /api/owners/admin/2
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: multipart/form-data

{
  "name": "Updated Owner Name",
  "email": "updated.owner@example.com"
}
```

**Response (Success - 200):**
```json
{
  "message": "Pemilik berhasil diperbarui.",
  "data": {
    "id": 2,
    "name": "Updated Owner Name",
    "email": "updated.owner@example.com"
  }
}
```

#### 5. Delete Owner (DELETE /api/admin/owners/{id})
Remove an owner from the system.

**Request:**
```
DELETE /api/admin/owners/2
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Response (Success - 200):**
```json
{
  "message": "Pemilik berhasil dihapus."
}
```

### Property Controller

#### 1. Get All Properties (GET /api/owner/properties)
Retrieve a list of all properties owned by the authenticated owner.

**Request:**
```
GET /api/owner/properties
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
```

**Response (Success - 200):**
```json
{
  "data": [
    {
      "id": 1,
      "name": "Villa Indah",
      "address": "Jl. Raya No. 123",
      "price": 500000,
      "description": "Villa dengan pemandangan indah",
      "user_id": 2,
      "created_at": "2023-05-15T10:00:00.000000Z",
      "updated_at": "2023-05-15T10:00:00.000000Z",
      "photos": [
        {
          "id": 1,
          "img": "property_images/xxxxxx.jpg",
          "img_main": true
        },
        {
          "id": 2,
          "img": "property_images/yyyyyy.jpg",
          "img_main": false
        }
      ]
    }
  ]
}
```

#### 2. Create Property (POST /api/owner/properties)
Create a new property listing.

**Request:**
```
POST /api/owner/properties
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: multipart/form-data

{
  "name": "Villa Baru",
  "address": "Jl. Baru No. 456",
  "price": 750000,
  "description": "Villa baru dengan fasilitas lengkap",
  "main_image": [file gambar utama],
  "additional_images[]": [file gambar 1],
  "additional_images[]": [file gambar 2]
}
```

**Response (Success - 201):**
```json
{
  "data": {
    "id": 2,
    "name": "Villa Baru",
    "address": "Jl. Baru No. 456",
    "price": 750000,
    "description": "Villa baru dengan fasilitas lengkap",
    "user_id": 2,
    "created_at": "2023-05-15T12:00:00.000000Z",
    "updated_at": "2023-05-15T12:00:00.000000Z",
    "photos": [
      {
        "id": 3,
        "img": "property_images/zzzzzz.jpg",
        "img_main": true
      },
      {
        "id": 4,
        "img": "property_images/aaaaaa.jpg",
        "img_main": false
      },
      {
        "id": 5,
        "img": "property_images/bbbbbb.jpg",
        "img_main": false
      }
    ]
  }
}
```

#### 3. Update Property (PUT /api/owner/properties/{id})
Update details of an existing property.

**Request:**
```
PUT /api/owner/properties/1
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: multipart/form-data

{
  "name": "Villa Indah Updated",
  "price": 600000,
  "deleted_image_ids[]": 2,
  "additional_images[]": [file gambar baru]
}
```

**Response (Success - 200):**
```json
{
  "data": {
    "id": 1,
    "name": "Villa Indah Updated",
    "address": "Jl. Raya No. 123",
    "price": 600000,
    "description": "Villa dengan pemandangan indah",
    "user_id": 2,
    "created_at": "2023-05-15T10:00:00.000000Z",
    "updated_at": "2023-05-15T13:00:00.000000Z",
    "photos": [
      {
        "id": 1,
        "img": "property_images/xxxxxx.jpg",
        "img_main": true
      },
      {
        "id": 6,
        "img": "property_images/cccccc.jpg",
        "img_main": false
      }
    ]
  }
}
```

### Room Controller

#### 1. Create Room (POST /api/owner/rooms)
Create a new room within a property.

**Request:**
```
POST /api/owner/rooms
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: multipart/form-data

{
  "property_id": 1,
  "name": "Kamar Deluxe",
  "capacity": 2,
  "price": 300000,
  "description": "Kamar luas dengan fasilitas lengkap",
  "availabilities[][date]": "2023-06-01",
  "availabilities[][stock]": 5,
  "availabilities[][date]": "2023-06-02",
  "availabilities[][stock]": 5
}
```

**Response (Success - 201):**
```json
{
  "data": {
    "id": 1,
    "property_id": 1,
    "name": "Kamar Deluxe",
    "capacity": 2,
    "price": 300000,
    "description": "Kamar luas dengan fasilitas lengkap",
    "created_at": "2023-05-15T14:00:00.000000Z",
    "updated_at": "2023-05-15T14:00:00.000000Z",
    "availabilities": [
      {
        "id": 1,
        "date": "2023-06-01",
        "stock": 5,
        "available": true
      },
      {
        "id": 2,
        "date": "2023-06-02",
        "stock": 5,
        "available": true
      }
    ],
    "property": {
      "id": 1,
      "name": "Villa Indah Updated",
      "address": "Jl. Raya No. 123"
    }
  }
}
```

#### 2. Update Room (PUT /api/owner/rooms/{id})
Update details of an existing room.

**Request:**
```
PUT /api/owner/rooms/1
Accept: application/json
Authorization: Bearer 2|Xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
Content-Type: multipart/form-data

{
  "price": 350000,
  "availabilities[][date]": "2023-06-01",
  "availabilities[][stock]": 3,
  "availabilities[][date]": "2023-06-03",
  "availabilities[][stock]": 4
}
```

**Response (Success - 200):**
```json
{
  "data": {
    "id": 1,
    "property_id": 1,
    "name": "Kamar Deluxe",
    "capacity": 2,
    "price": 350000,
    "description": "Kamar luas dengan fasilitas lengkap",
    "created_at": "2023-05-15T14:00:00.000000Z",
    "updated_at": "2023-05-15T15:00:00.000000Z",
    "availabilities": [
      {
        "id": 3,
        "date": "2023-06-01",
        "stock": 3,
        "available": true
      },
      {
        "id": 4,
        "date": "2023-06-03",
        "stock": 4,
        "available": true
      }
    ],
    "property": {
      "id": 1,
      "name": "Villa Indah Updated",
      "address": "Jl. Raya No. 123"
    }
  }
}
```

### Public Property Controller

#### 1. Get All Properties (GET /api/user/properties)
List all available properties with optional filtering.

**Request:**
```
GET /api/user/properties?city=Jakarta&type=villa&name=Indah
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Properti berhasil diambil",
  "data": {
    "data": [
      {
        "id": 1,
        "name": "Villa Indah",
        "address": "Jl. Raya No. 123, Jakarta",
        "price": 500000,
        "description": "Villa dengan pemandangan indah",
        "photos": [
          {
            "id": 1,
            "img": "property_images/xxxxxx.jpg",
            "img_main": true
          }
        ]
      }
    ],
    "links": {
      "first": "http://example.com/api/user/properties?page=1",
      "last": "http://example.com/api/user/properties?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "path": "http://example.com/api/user/properties",
      "per_page": 10,
      "to": 1,
      "total": 1
    }
  }
}
```

#### 2. Get Property Detail (GET /api/user/properties/{id})
Get details about a specific property.

**Request:**
```
GET /api/user/properties/1
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Detail properti berhasil diambil",
  "data": {
    "id": 1,
    "name": "Villa Indah",
    "address": "Jl. Raya No. 123, Jakarta",
    "price": 500000,
    "description": "Villa dengan pemandangan indah",
    "photos": [
      {
        "id": 1,
        "img": "property_images/xxxxxx.jpg",
        "img_main": true
      }
    ],
    "rooms": [
      {
        "id": 1,
        "name": "Kamar Deluxe",
        "capacity": 2,
        "price": 300000,
        "description": "Kamar luas dengan fasilitas lengkap"
      }
    ]
  }
}
```

#### 3. Get Property Rooms (GET /api/user/properties/{id}/rooms)
List all rooms available within a property.

**Request:**
```
GET /api/user/properties/1/rooms
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Kamar berhasil diambil",
  "data": [
    {
      "id": 1,
      "name": "Kamar Deluxe",
      "capacity": 2,
      "price": 300000,
      "description": "Kamar luas dengan fasilitas lengkap",
      "availabilities": [
        {
          "date": "2023-06-01",
          "available": true,
          "stock": 5
        }
      ]
    }
  ]
}
```

#### 4. Get Room Detail (GET /api/user/properties/{id}/rooms/{id})
Get detailed information about a specific room.

**Request:**
```
GET /api/user/properties/1/rooms/1
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Detail kamar berhasil diambil",
  "data": {
    "room": {
      "id": 1,
      "name": "Kamar Deluxe",
      "capacity": 2,
      "price": 300000,
      "description": "Kamar luas dengan fasilitas lengkap",
      "property": {
        "id": 1,
        "name": "Villa Indah",
        "address": "Jl. Raya No. 123, Jakarta"
      },
      "availabilities": [
        {
          "date": "2023-06-01",
          "available": true,
          "stock": 5
        }
      ]
    },
    "booking_info": {
      "min_date": "2023-05-20",
      "max_quantity": 5,
      "price_range": {
        "default": 300000,
        "custom": {
          "2023-06-01": 350000
        }
      }
    }
  }
}
```

### Booking Controller

#### 1. Create Booking (POST /api/user/properties/{id}/rooms/{id}/bookings)
Make a new booking for a specific room.

**Request:**
```
POST /api/user/properties/1/rooms/1/bookings
Accept: application/json
Authorization: Bearer [token]
Content-Type: multipart/form-data

{
  "check_in": "2023-06-01",
  "check_out": "2023-06-03",
  "guest_count": 2,
  "quantity": 1,
  "nik": "1234567890123456",
  "ktp_img": [file KTP],
  "address": "Jl. Contoh No. 123",
  "gender": "L"
}
```

**Response (Success - 201):**
```json
{
  "message": "Pemesanan berhasil dibuat",
  "data": {
    "id": 1,
    "user_id": 3,
    "property_id": 1,
    "room_id": 1,
    "check_in": "2023-06-01",
    "check_out": "2023-06-03",
    "guest_count": 2,
    "quantity": 1,
    "total_price": 650000,
    "status": "pending",
    "created_at": "2023-05-20T10:00:00.000000Z",
    "updated_at": "2023-05-20T10:00:00.000000Z",
    "room": {
      "id": 1,
      "name": "Kamar Deluxe"
    },
    "property": {
      "id": 1,
      "name": "Villa Indah"
    }
  }
}
```

**Response (Error - 400):**
```json
{
  "message": "Kamar tidak tersedia pada tanggal berikut",
  "date": "2023-06-02",
  "available": 0,
  "requested": 1
}
```

#### 2. Get User Bookings (GET /api/user/bookings)
List all bookings made by the authenticated user.

**Request:**
```
GET /api/user/bookings
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Pemesanan pengguna berhasil diambil",
  "data": {
    "data": [
      {
        "id": 1,
        "check_in": "2023-06-01",
        "check_out": "2023-06-03",
        "status": "pending",
        "total_price": 650000,
        "property": {
          "id": 1,
          "name": "Villa Indah"
        },
        "room": {
          "id": 1,
          "name": "Kamar Deluxe"
        },
        "payments": []
      }
    ],
    "links": {
      "first": "http://example.com/api/user/bookings?page=1",
      "last": "http://example.com/api/user/bookings?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "path": "http://example.com/api/user/bookings",
      "per_page": 10,
      "to": 1,
      "total": 1
    }
  }
}
```

#### 3. Get User Booking Detail (GET /api/user/bookings/{id})
Get detailed information about a specific booking.

**Request:**
```
GET /api/user/bookings/1
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Detail pemesanan berhasil diambil",
  "data": {
    "id": 1,
    "check_in": "2023-06-01",
    "check_out": "2023-06-03",
    "status": "pending",
    "total_price": 650000,
    "property": {
      "id": 1,
      "name": "Villa Indah",
      "address": "Jl. Raya No. 123, Jakarta"
    },
    "room": {
      "id": 1,
      "name": "Kamar Deluxe",
      "capacity": 2,
      "price": 300000
    },
    "user_profile": {
      "nik": "1234567890123456",
      "address": "Jl. Contoh No. 123",
      "gender": "L",
      "ktp_img": "ktp_images/yyyyyy.jpg"
    },
    "payments": []
  }
}
```

#### 4. Get Owner Bookings (GET /api/owner/bookings)
List all bookings made for properties owned by the authenticated owner.

**Request:**
```
GET /api/owner/bookings
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Pemesanan berhasil diambil",
  "data": {
    "data": [
      {
        "id": 1,
        "check_in": "2023-06-01",
        "check_out": "2023-06-03",
        "status": "pending",
        "total_price": 650000,
        "user": {
          "id": 3,
          "name": "John Doe",
          "email": "john.doe@example.com"
        },
        "property": {
          "id": 1,
          "name": "Villa Indah"
        },
        "room": {
          "id": 1,
          "name": "Kamar Deluxe"
        }
      }
    ],
    "links": {
      "first": "http://example.com/api/owner/bookings?page=1",
      "last": "http://example.com/api/owner/bookings?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "path": "http://example.com/api/owner/bookings",
      "per_page": 10,
      "to": 1,
      "total": 1
    }
  }
}
```

### Payment Controller

#### 1. Create Payment (POST /api/user/bookings/{id}/payments)
Create a new payment for a booking.

**Request:**
```
POST /api/user/bookings/1/payments
Accept: application/json
Authorization: Bearer [token]
Content-Type: multipart/form-data

{
  "method": "Transfer",
  "transfer_proof": [file bukti transfer]
}
```

**Response (Success - 201):**
```json
{
  "message": "Pembayaran berhasil dibuat",
  "data": {
    "id": 1,
    "booking_id": 1,
    "amount": 650000,
    "method": "Transfer",
    "status": "pending",
    "transfer_proof": "payment_proofs/zzzzzz.jpg",
    "created_at": "2023-05-20T11:00:00.000000Z",
    "updated_at": "2023-05-20T11:00:00.000000Z"
  }
}
```

#### 2. Get User Payments (GET /api/user/payments)
List all payments made by the authenticated user.

**Request:**
```
GET /api/user/payments
Accept: application/json
Authorization: Bearer [token]
```

**Response (Success - 200):**
```json
{
  "message": "Pembayaran berhasil diambil",
  "data": {
    "data": [
      {
        "id": 1,
        "amount": 650000,
        "method": "Transfer",
        "status": "pending",
        "created_at": "2023-05-20T11:00:00.000000Z",
        "booking": {
          "id": 1,
          "property": {
            "id": 1,
            "name": "Villa Indah"
          },
          "rooms": [
            {
              "id": 1,
              "name": "Kamar Deluxe"
            }
          ]
        }
      }
    ],
    "links": {
      "first": "http://example.com/api/user/payments?page=1",
      "last": "http://example.com/api/user/payments?page=1",
      "prev": null,
      "next": null
    },
    "meta": {
      "current_page": 1,
      "from": 1,
      "last_page": 1,
      "path": "http://example.com/api/user/payments",
      "per_page": 10,
      "to": 1,
      "total": 1
    }
  }
}
```

#### 3. Update Payment Status (POST /api/owner/payments/{id}/status)
Update the status of a payment (owner only).

**Request:**
```
POST /api/owner/payments/1/status
Accept: application/json
Authorization: Bearer [token]
Content-Type: multipart/form-data

{
  "status": "success"
}
```

**Response (Success - 200):**
```json
{
  "message": "Status pembayaran berhasil diperbarui",
  "data": {
    "id": 1,
    "booking_id": 1,
    "amount": 650000,
    "method": "Transfer",
    "status": "success",
    "paid_at": "2023-05-20T12:00:00.000000Z",
    "transfer_proof": "payment_proofs/zzzzzz.jpg",
    "created_at": "2023-05-20T11:00:00.000000Z",
    "updated_at": "2023-05-20T12:00:00.000000Z"
  }
}
```

## Use Cases

### User Flow
1. User registers or logs in to get an authentication token
2. User browses available properties (can filter by city, type, etc.)
3. User views details of a specific property
4. User views available rooms in the property
5. User checks availability and pricing of a specific room
6. User makes a booking by providing personal information
7. User uploads payment proof
8. User can view their booking history and payment status

### Owner Flow
1. Owner logs in to get an authentication token
2. Owner creates or updates their property listings
3. Owner manages rooms within their properties
4. Owner sets availability and pricing for rooms
5. Owner views bookings made for their properties
6. Owner processes payments and updates payment status

### Admin Flow
1. Admin manages owner accounts
2. Admin can create, update, or delete owner accounts

## Error Handling
The API returns appropriate HTTP status codes along with error messages:

- 200 OK: The request was successful
- 201 Created: A new resource was successfully created
- 400 Bad Request: The request contains invalid parameters
- 401 Unauthorized: Authentication is required or credentials are invalid
- 403 Forbidden: The authenticated user doesn't have permission to access the resource
- 404 Not Found: The requested resource doesn't exist
- 422 Unprocessable Entity: The request data was invalid
- 500 Internal Server Error: An error occurred on the server
