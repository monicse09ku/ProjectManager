# Project Manager

A modern Laravel + Vue 3 project management application with role-based authentication, task management, and client/project tracking.

## Quick Start

Follow these steps to set up and run the application:

### 1. Clone the Repository

```bash
git clone https://github.com/monicse09ku/ProjectManager.git
cd ProjectManager
```

### 2. Update Environment File

Copy the example environment file and configure it with your database settings:

```bash
cp .env.example .env
```

Update the `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=project_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Install Dependencies

```bash
composer install
```

### 4. Run Migrations

```bash
php artisan migrate
```

### 5. Seed the Database

This will create sample data including clients, projects, users, and tasks:

```bash
php artisan db:seed
```

### 6. Start Development Server

```bash
composer run dev
```

The application will be available at `http://localhost:8000`

## Login Credentials

After seeding the database, use these credentials to log in:

### Admin User
- **Email**: admin@example.com
- **Password**: password
- **Role**: Admin (full access to all features)

### Regular Users
- **Email**: john@example.com
- **Password**: password
- **Role**: User (can only view assigned tasks)

- **Email**: jane@example.com
- **Password**: password
- **Role**: User

- **Email**: bob@example.com
- **Password**: password
- **Role**: User

## Features

- **Role-Based Authentication**: Separate dashboards for Admin and User roles
- **Client Management**: Create, read, update, and delete clients
- **Project Management**: Manage projects associated with clients
- **Task Management**: Assign tasks to users with deadline tracking
- **Dashboard Analytics**: View client, project, and task statistics
- **User Task View**: Regular users can view only their assigned tasks
- **Email Notifications**: Automated notifications for task assignments
- **Queue Jobs**: Background job processing for notifications

## What's Included in the Seeder

The database seeder includes:

- **1 Admin User** with full system access
- **3 Regular Users** who can view their assigned tasks
- **Sample Clients** for testing client management
- **Sample Projects** associated with clients
- **Sample Tasks** assigned to users

## Technology Stack

- **Backend**: Laravel 12
- **Frontend**: Vue 3 + TypeScript + Tailwind CSS
- **Database**: MySQL/MariaDB
- **Authentication**: Laravel Fortify + Laravel Sanctum (API)
- **Testing**: Pest PHP

## API Documentation

### Overview

The application provides a RESTful API with token-based authentication using Laravel Sanctum. All API endpoints are prefixed with `/api`.

### Authentication

#### Login
```http
POST /api/login
Content-Type: application/json

{
    "email": "admin@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": { ... },
        "token": "4|abc123..."
    }
}
```

#### Get Authenticated User
```http
GET /api/user
Authorization: Bearer {token}
```

#### Logout
```http
POST /api/logout
Authorization: Bearer {token}
```

### Postman Collection

A complete Postman collection is included for easy API testing and exploration:

**File:** `ProjectManager.postman_collection.json` (located in root directory)

**What's Included:**
- All authentication endpoints (login, logout, get user)
- Complete CRUD operations for Clients, Projects, and Tasks
- Pre-configured request examples with sample payloads
- Environment variables for token management
- Ready-to-use test scenarios for both Admin and User roles

**To Use:**
1. Open Postman
2. Click Import → Upload Files
3. Select `ProjectManager.postman_collection.json`
4. All API endpoints will be available in your Postman workspace

All authenticated endpoints require the `Authorization: Bearer {token}` header, which you'll receive after login.

### API Response Format

All API responses follow a consistent format:

**Success Response:**
```json
{
    "success": true,
    "message": "Operation successful",
    "data": { ... }
}
```

**Error Response:**
```json
{
    "success": false,
    "message": "Error message",
    "errors": { ... }
}
```

### Authorization Rules

- **Admin**: Full access to all endpoints
- **User**: 
  - Can only view tasks assigned to them
  - Can only update deadline and status of their assigned tasks

## Email Notifications & Queue Jobs

### Overview

The application uses **Laravel Queue Jobs** to handle email notifications asynchronously, ensuring that email sending doesn't block the main application flow.

### Email Notifications

**Task Assignment Notifications** are automatically sent when:
- A new task is created and assigned to a user
- An existing task is reassigned to a different user

**Email Details:**
- **Recipients**: Assigned user's email address
- **Content**: Task details including title, project, deadline, and status
- **Trigger**: Automatically dispatched via queue job

### Queue Configuration

The queue system is configured to use the **database** driver for job storage.

**Database Tables:**
- `jobs` - Stores pending queue jobs
- `failed_jobs` - Stores failed jobs for debugging and retry

**Queue Settings (`.env`):**
```env
QUEUE_CONNECTION=database
MAIL_MAILER=log
```

## Testing

The application uses **Pest PHP** for testing with comprehensive API test coverage.

### Running Tests

Run all tests:
```bash
php artisan test
```

Run specific test suites:
```bash
# Client API tests
php artisan test --filter=ClientApiTest

# Project API tests
php artisan test --filter=ProjectApiTest

# Task API tests
php artisan test --filter=TaskApiTest
```

### Test Coverage

The test suite includes:

#### Client API Tests (12 tests)

#### Project API Tests (21 tests)

#### Task API Tests (23 tests)

**Total: 94 tests with 131+ assertions**